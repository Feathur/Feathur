#!/usr/bin/python
# VMBalancer for Numa Class Systems
# Python 2.7.X
# Shankhadeep Shome
# Version 0.1

import os
import sys
import subprocess

# Locating programs in Python, use the following snip to locate programs in python from http://jimmyg.org/blog/2009/working-with-python-subprocess.html

def whereis(program):
	for path in os.environ.get('PATH', '').split(':'):
        	if os.path.exists(os.path.join(path, program)) and \
           	not os.path.isdir(os.path.join(path, program)):
			return os.path.join(path, program)
	return None

# Python class numa node

class NumaNode:
	def __init__(self,node_number,cpu_list,memorysize,memoryfree,processcount):
        	self.node_number = node_number
        	self.cpu_list = cpu_list
		self.memorysize = memorysize
		self.memoryfree = memoryfree
		self.processcount = processcount

class VMprocessInfo:
	def __init__(self,vm_name,processid,vcpu_num):
		self.vm_name = vm_name
		self.processid = processid
		self.vcpu_num = vcpu_num

#Python numactl output parsing

def parsenumactl():
	process = subprocess.Popen(['numactl --hardware | grep cpu'], shell=True, stdout=subprocess.PIPE)
	cpu_list = process.communicate()
	process = subprocess.Popen(['numactl --hardware | grep size'], shell=True, stdout=subprocess.PIPE)
	mem_list = process.communicate()
	process = subprocess.Popen(['numactl --hardware | grep free'], shell=True, stdout=subprocess.PIPE)
	memfree_list = process.communicate()
	cpu_list = str.splitlines(cpu_list[0])
	mem_list = str.splitlines(mem_list[0])
	memfree_list = str.splitlines(memfree_list[0])
	node_list = {}
	for index in range(len(cpu_list)):
		node_list[index]=NumaNode(str.split(cpu_list[index])[1], str.split(cpu_list[index])[3:], str.split(mem_list[index])[3], str.split(memfree_list[index])[3], 0)
	return node_list

# Returns a list of VM processes, pids and their vcpu count

def parse_vmlist(libvirt_run_dir):
	process = subprocess.Popen(['virsh list | tail -n +3'], shell=True, stdout=subprocess.PIPE)
	vmname_list = process.communicate()
	vmname_list = str.splitlines(vmname_list[0])
	vm_list = {}
	for index in range(len(vmname_list)):
		if not str.split(vmname_list[index]):
			continue
		vm_name = str.split(vmname_list[index])[1]
		runcmd = 'virsh dominfo ' + vm_name + ' | grep CPU | head -n 1'
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		vcpu_count = str.split(str.splitlines(process.communicate()[0])[0])[1]
		runcmd = 'grep pid ' + libvirt_run_dir + vm_name + '.xml' +  ' | head -n 1 | egrep -oh \'([0-9])*\''
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		vm_pid = str.splitlines(process.communicate()[0])[0]
		vm_list[index]=VMprocessInfo(vm_name, vm_pid, vcpu_count)
	return vm_list
	
#Python kvm process list parsing

def parsekvmprocesslist():
	process = subprocess.Popen(['ps -e | grep kvm | grep -v -'], shell=True, stdout=subprocess.PIPE)
	vmraw_list = process.communicate()
	vmraw_list = str.splitlines(vmraw_list[0])
	vmpid_list = []
	for index in range(len(vmraw_list)):
		vmpid_list.append(str.split(vmraw_list[index])[0])
	return vmpid_list

#Python kvm cset parsing, returns all the cset processes for a list of numa nodes

def parse_cset(node_list):
	process_list = []
	for index in range(len(node_list)):
		runcmd = 'cset proc -l VMBS' + node_list[index].node_number + ' | tail -n +4'
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		for index in str.splitlines(process.communicate()[0]):
			process_list.append(str.split(index)[1])
	return process_list

# Returns the number of processes in the node list

def parse_cset_processcount(node_list):
	for index in range(len(node_list)):
		runcmd = 'cset proc -l VMBS' + node_list[index].node_number + ' | tail -n +4 | wc -l'
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		node_list[index].processcount = int(str.splitlines(process.communicate()[0])[0])
	return node_list

# Returns the number of vcpus in the node list, using virsh to extract the vcpus per VM

def parse_cset_processcount_vcpu(node_list, virsh_vm_list):
        for index in range(len(node_list)):
		for index2 in range(len(virsh_vm_list)):
                	runcmd = 'cset proc -l VMBS' + node_list[index].node_number + ' | tail -n +4 | grep ' + virsh_vm_list[index2].processid  +  ' | wc -l'
                	process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
			if int(str.splitlines(process.communicate()[0])[0]) > 0:
                		node_list[index].processcount += int(virsh_vm_list[index2].vcpu_num)
        return node_list

#Python create csets, create the proper set, if they exist they just get modified by cset

def create_cset(node_list):
	for index in range(len(node_list)):
		runcmd = '/usr/bin/cset set -c ' + (','.join(node_list[index].cpu_list)) + ' -m ' + node_list[index].node_number + ' -s VMBS' + node_list[index].node_number
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		for index in (str.splitlines(process.communicate()[0])):
			print index
	return 1

#delete VM balancer csets and return processes to the parents

def delete_cset(node_list):
	for index in range(len(node_list)):
		runcmd = '/usr/bin/cset set -d -s VMBS' + node_list[index].node_number
		process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
		for index in (str.splitlines(process.communicate()[0])):
			print index
	return 1

# Checks if system is configured for numa

def numa_capable():
	numa_capable = 0
	process = subprocess.Popen(['numactl --hardware | grep available'], shell=True, stdout=subprocess.PIPE)
	test_available = process.communicate()
	test_available = str.splitlines(test_available[0])
	if int(str.split(test_available[0])[1]) > 1:
		numa_capable = 1
	return numa_capable

# Check if system has the required utilities, cset numactl etc

def required_utilities(utility_list):
	required_utilities = 1
	for index in utility_list:
		if whereis(index) == None:
			print 'Cannot locate ' + index + ' in path!'
			required_utilities = 0
		else:
			print 'Found ' + index + ' at ' + whereis(index)
	return required_utilities

# Checks if VM processid is a member of a numa_node set

def member_of_numa_node(processid, node_number):
	runcmd = 'cset proc -l VMBS' + node_number + ' | tail -n +4 | grep ' + processid
	process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
	if len(process.communicate()[0]) > 0:
		return 1
	else:
		return 0

# Add a process and it's threads to a cpuset

def addkvmprocess(processid, numa_node):
	runcmd = 'cset proc --move ' + processid + ' VMBS' + numa_node + ' --threads'
	process = subprocess.Popen([runcmd], shell=True, stdout=subprocess.PIPE)
	for index in (str.splitlines(process.communicate()[0])):
                        print index
	return 1

# Returns sorted list of numa nodes and their process counts for scheduling

def sorted_process_count_list(node_list):
        sorted_list = []
        for index in range(len(node_list)):
                sorted_list.append([node_list[index].node_number, node_list[index].processcount])
        sorted_list = sorted(sorted_list, key = lambda element: element[1])
        return sorted_list

# Rebalancer maximizes cpu usage. First place all newly instantiated VM processes 
# into the cpusets. Then rebalance one of the existing VMs if nessesary 
#(run multiple times for full rebalance, there is a configured threshold so 
# VMs aren't swapped back and forth between two nodes)

def vm_rebalancer_cpu(node_list,virsh_vm_list):
	raw_vm_list = parsekvmprocesslist()
	cset_process_list = parse_cset(node_list)
	vm_list_unshielded = list(set(raw_vm_list) - set(cset_process_list))
	vm_list_shielded = list(set(raw_vm_list) & set(cset_process_list))
	# Add new kvm processes into the cpusets
	for index in vm_list_unshielded:
		node_list = parse_cset_processcount_vcpu(node_list, virsh_vm_list)
		sorted_node_list = sorted_process_count_list(node_list)
		addkvmprocess(index, sorted_node_list.pop(0)[0])
	# Update the process count on each numa node and re-sort
	node_list = parse_cset_processcount_vcpu(node_list, virsh_vm_list)
        sorted_node_list = sorted_process_count_list(node_list)
	# Move a single shielded process group from the most used node to the least used node
	# if it meets the threshold, which is twice the number of cpus in the most used node.
	# This way its more likely that cpu nodes with more processors will tend to hold on to 
	# their VM processes longer. By moving only one active VM per sheduling run, the admin
	# has better control over the process.
	most_used_node = sorted_node_list.pop()
	least_used_node = sorted_node_list.pop(0)
	if (most_used_node[1] - least_used_node[1]) > (2 * len(node_list[int(most_used_node[0])].cpu_list)):
		for index in vm_list_shielded:
			if member_of_numa_node(index, most_used_node[0]) == 1:
				addkvmprocess(index, least_used_node[0])
				break	
	return 1

# Same as above but only schedules up to a max(num. of numa nodes) vm processes per scheduling run

def vm_rebalancer_cpu_fast(node_list,virsh_vm_list):
        raw_vm_list = parsekvmprocesslist()
        cset_process_list = parse_cset(node_list)
        vm_list_unshielded = list(set(raw_vm_list) - set(cset_process_list))
        vm_list_shielded = list(set(raw_vm_list) & set(cset_process_list))
	node_list = parse_cset_processcount_vcpu(node_list, virsh_vm_list)
        sorted_node_list = sorted_process_count_list(node_list)
        # Add new kvm processes into the cpusets
        for index in vm_list_unshielded:
		if len(sorted_node_list) > 0:
	                addkvmprocess(index, sorted_node_list.pop(0)[0])
        # Update the process count on each numa node and re-sort
        node_list = parse_cset_processcount_vcpu(node_list, virsh_vm_list)
        sorted_node_list = sorted_process_count_list(node_list)
        # Move a single shielded process group from the most used node to the least used node
        # if it meets the threshold, which is twice the number of cpus in the most used node.
        # This way its more likely that cpu nodes with more processors will tend to hold on to
        # their VM processes longer. By moving only one active VM per sheduling run, the admin
        # has better control over the process.
        most_used_node = sorted_node_list.pop()
        least_used_node = sorted_node_list.pop(0)
        if (most_used_node[1] - least_used_node[1]) > (2 * len(node_list[int(most_used_node[0])].cpu_list)):
                for index in vm_list_shielded:
                        if member_of_numa_node(index, most_used_node[0]) == 1:
                                addkvmprocess(index, least_used_node[0])
                                break
        return 1
		
# Rebalancer that maximizes memory usage, placeholder for some memory centric rebalancer

def vm_rebalancer_memory(numa_list):
	return 1

# Rebalance function and wrapper

def vm_rebalancer(numa_node, rebalance_type, virsh_vm_list):
	if rebalance_type == 'cpu_balancer':
		vm_rebalancer_cpu(numa_node, virsh_vm_list)
	elif rebalance_type == 'cpu_balancer_fast':
                vm_rebalancer_cpu_fast(numa_node, virsh_vm_list)
	elif rebalance_type == 'mem_maximizer':
                vm_rebalancer_mem(numa_node)
	else:
		print 'Unknown balancer type'
		return 0
	return 1

# Main Function

if __name__=="__main__":
	if len(sys.argv) > 1:
        	print 'Utility does not take arguments.. yet'
		sys.exit(1)
	if required_utilities(['cset', 'numactl', 'virsh']) == 0:
		print 'Utilities Missing! Exiting..'
		sys.exit(1)
	if numa_capable() == 0:
		print 'Machine not numa capable or memory is not configured with a numa layout'
		sys.exit(1)
	# Parse numactl, the resulting dictionary should always be greater than 2
	numa_list = parsenumactl()
	if len(numa_list) < 2:
		print 'Cannot parse numactl properly'
                sys.exit(1)
	# Create cpu sets
	if create_cset(numa_list) == 0:
		print 'Program Error: could not create cpu sets using cset, see cset documentation for furthur details'
		sys.exit(1)
	# Create a list of running VMs from virsh
	virsh_vm_list = parse_vmlist('/var/run/libvirt/qemu/')
	if not virsh_vm_list:
		print 'No running VMs using libvirt'
		sys.exit(0)
	# Run vm_rebalancer
	if vm_rebalancer(numa_list, 'cpu_balancer_fast', virsh_vm_list) == 0:
		print 'Rebalancer failed to execute properly'
                sys.exit(1)
	sys.exit(0)
