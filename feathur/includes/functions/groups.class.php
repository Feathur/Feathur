<?php

class Group extends CPHPDatabaseRecordClass
{

  public $table_name = 'groups';
  public $id_field = 'id';
  public $fill_query = 'SELECT * FROM groups WHERE `id` = :Id';
  public $verify_query = 'SELECT * FROM groups WHERE `id` = :Id';
  public $query_cache = 1;

  public $prototype = array(
    'string' => array(
      'Name' => 'name'
    )
  );

  public static function array_groups()
  {

    global $database;

    $sGroups = $database->CachedQuery('SELECT * FROM groups', array());

    foreach($sGroups->data as $value)
    {
      $sGroupList[] = array('id' => $value['id'], 'name' => $value['name']);
    }

    return $sGroupList;
  }

}
