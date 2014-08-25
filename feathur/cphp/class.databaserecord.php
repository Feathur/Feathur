<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 *
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if($_CPHP !== true) { die(); }

abstract class CPHPDatabaseRecordClass extends CPHPBaseClass
{

  public $fill_query = "";
  public $verify_query = "";
  public $table_name = "";
  public $query_cache = 60;
  public $id_field = "Id";
  public $autoloading = true;
  public $prototype = array();
  public $prototype_render = array();
  public $prototype_export = array();
  public $uData = array();
  public $sId = 0;

  public function __construct($uDataSource = 0, $defaultable = null)
  {
    global $cphp_config;
    if(!isset($cphp_config->class_map))
    {
      die("No class map was specified. Refer to the CPHP manual for instructions.");
    }
    $this->ConstructDataset($uDataSource);
    $this->EventConstructed();
  }

  public function __get($name)
  {
    /* TODO: Don't overwrite current value in uVariable when sVariable is requested and uVariable is already set. */
    if($name[0] == "s" || $name[0] == "u")
    {
      $actual_name = substr($name, 1);
      $found = false;
      foreach($this->prototype as $type => $dataset)
      {
        if(isset($dataset[$actual_name]))
        {
          $found = true;
          $found_type = $type;
          $found_field = $dataset[$actual_name];
        }
      }
      if($found === false)
      {
        $classname = get_class($this);
        throw new PrototypeException("The {$actual_name} variable was not found in the prototype of the {$classname} class.");
      }
      $this->SetField($found_type, $actual_name, $found_field);
      return $this->$name;
    }
  }

  public function RefreshData()
  {
    $this->PurgeCache();
    $this->ConstructDataset($this->sId, 0);
    if($this->autoloading === true)
    {
      $this->PurgeVariables();
    }
  }

  public function PurgeVariables()
  {
    foreach($this->prototype as $type => $dataset)
    {
      foreach($dataset as $key => $field)
      {
        $variable_name_safe = "s" . $key;
        $variable_name_unsafe = "u" . $key;
        unset($this->$variable_name_safe);
        unset($this->$variable_name_unsafe);
      }
    }
  }

  public function ConstructDataset($uDataSource, $expiry = -1)
  {
    global $database;
    $bind_datasets = true;
    if (is_numeric($uDataSource))
    {
      if ($uDataSource != 0)
      {
        if (!empty($this->fill_query))
        {
          $this->sId = (is_numeric($uDataSource)) ? $uDataSource : 0;
          $expiry = ($expiry == -1) ? $this->query_cache : $expiry;
          /* Use PDO to fetch the object from the database. */
          if ($result = $database->CachedQuery($this->fill_query, array(":Id" => $this->sId), $expiry))
          {
            $uDataSource = $result->data[0];
          } else
          {
            $classname = get_class($this);
            throw new NotFoundException("Could not locate {$classname} {$uDataSource} in database.", 0, null, "");
          }
        } else
        {
          $classname = get_class($this);
          throw new PrototypeException("No fill query defined for {$classname} class.");
        }
      } else
      {
        $bind_datasets = false;
        $this->FillDefaults();
      }
    } elseif (is_object($uDataSource))
    {
      if (isset($uDataSource->data[0]))
      {
        $uDataSource = $uDataSource->data[0];
      } else
      {
        throw new NotFoundException("No result set present in object.");
      }
    } elseif (is_array($uDataSource))
    {
      if (isset($uDataSource[0]))
      {
        $uDataSource = $uDataSource[0];
      }
    } else
    {
      $classname = get_class($this);
      throw new ConstructorException("Invalid type passed on to constructor for object of type {$classname}.");
    }
    if ($bind_datasets === true)
    {
      $this->sId = (is_numeric($uDataSource[$this->id_field])) ? $uDataSource[$this->id_field] : 0;
      $this->uData = $uDataSource;
      if ($this->autoloading === false)
      {
        foreach($this->prototype as $type => $dataset)
        {
          $this->BindDataset($type, $dataset, $defaultable);
        }
      }
      $this->sFound = true;
    } else
    {
      $this->sFound = false;
    }
  }

  public function BindDataset($type, $dataset, $defaultable)
  {
    global $cphp_config;
    if (is_array($dataset))
    {
      foreach($dataset as $variable_name => $column_name)
      {
        $this->SetField($type, $variable_name, $column_name);
      }
    } else
    {
      $classname = get_class($this);
      throw new Exception("Invalid dataset passed on to {$classname}.BindDataset.");
    }
  }

  public function SetField($type, $variable_name, $column_name)
  {
    global $cphp_config;
    if (!isset($this->uData[$column_name]))
    {
      throw new Exception("The column name {$column_name} was not found in the resultset - ensure the prototype corresponds to the table schema.");
    }
    $original_value = $this->uData[$column_name];
    if ($original_value === "" && ($type == "timestamp" || $type == "numeric" || $type == "boolean"))
    {
      $variable_name_safe = "s" . $variable_name;
      $this->$variable_name_safe = null;
      $variable_name_unsafe = "u" . $variable_name;
      $this->$variable_name_unsafe = null;
    } else
    {
      switch($type)
      {
        case "string":
          $value = htmlspecialchars(stripslashes($original_value));
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "html":
          $value = filter_html(stripslashes($original_value));
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "simplehtml":
          $value = filter_html_strict(stripslashes($original_value));
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "nl2br":
          $value = nl2br(htmlspecialchars(stripslashes($original_value)), false);
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "numeric":
          $value = (is_numeric($original_value)) ? $original_value : 0;
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "timestamp":
          $value = unix_from_mysql($original_value);
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "boolean":
          $value = (empty($original_value)) ? false : true;
          $variable_type = CPHP_VARIABLE_SAFE;
          break;
        case "none":
          $value = $original_value;
          $variable_type = CPHP_VARIABLE_UNSAFE;
          break;
        default:
          $found = false;
          foreach(get_object_vars($cphp_config->class_map) as $class_type => $class_name)
          {
          if ($type == $class_type)
          {
            try
            {
              $value = new $class_name($original_value);
            } catch (NotFoundException $e)
            {
              $e->field = $variable_name;
              throw $e;
            }
            $variable_type = CPHP_VARIABLE_SAFE;
            $found = true;
          }
        }
        if ($found == false)
        {
          $classname = get_class($this);
          throw new Exception("Cannot determine type of dataset ({$type}) passed on to {$classname}.BindDataset.");
          break;
        }
      }
      if ($variable_type == CPHP_VARIABLE_SAFE)
      {
        $variable_name_safe = "s" . $variable_name;
        $this->$variable_name_safe = $value;
      }
      $variable_name_unsafe = "u" . $variable_name;
      $this->$variable_name_unsafe = $original_value;
    }
  }

  public function FillDefaults()
  {
    foreach($this->prototype as $type => $dataset)
    {
      switch($type)
      {
        case "string":
        case "simplehtml":
        case "html":
        case "nl2br":
        case "none":
          $safe_default_value = "";
          $unsafe_default_value = "";
          break;
        case "numeric":
          $safe_default_value = 0;
          $unsafe_default_value = "0";
          break;
        case "boolean":
          $safe_default_value = false;
          $unsafe_default_value = "0";
          break;
        case "timestamp":
          $safe_default_value = null;
          $unsafe_default_value = null;
          break;
        default:
          continue 2;
      }
      foreach($dataset as $property)
      {
        $safe_variable_name = "s" . $property;
        $this->$safe_variable_name = $safe_default_value;
        $unsafe_variable_name = "u" . $property;
        $this->$unsafe_variable_name = $unsafe_default_value;
      }
    }
  }

  public function DoRenderInternalTemplate()
  {
    /* DEPRECATED: Please do not use this function.
     * Class-specific templater functions have been discontinued. Instead, you can use
     * Templater::AdvancedParse for rendering templates without instantiating a Templater
     * yourself. */
    if(!empty($this->render_template))
    {
      $strings = array();
      foreach($this->prototype_render as $template_var => $object_var)
      {
        $variable_name = "s" . $object_var;
        $strings[$template_var] = $this->$variable_name;
      }
      return $this->DoRenderTemplate($this->render_template, $strings);
    } else
    {
      $classname = get_class($this);
      throw new Exception("Cannot render template: no template defined for {$classname} class.");
    }
  }

  public function InsertIntoDatabase($force_data = false)
  {
    global $cphp_config, $database;

    if (!empty($this->verify_query))
    {

      if (strpos($this->verify_query, ":Id") === false)
      {
        throw new DeprecatedException("Support for mysql_* has been removed from CPHP. Please update your queries to be in CachedPDO-style.");
      }

      if ($this->sId == 0)
      {
        $insert_mode = CPHP_INSERTMODE_INSERT;
      } else
      {
        if ($result = $database->CachedQuery($this->verify_query, array(":Id" => $this->sId), 0))
        {
          $insert_mode = CPHP_INSERTMODE_UPDATE;
        } else
        {
          $insert_mode = CPHP_INSERTMODE_INSERT;
        }
      }

      if ($force_data === true)
      {
        foreach($this->prototype as $type_key => $type_value)
        {
          foreach($type_value as $element_key => $element_value)
          {
            $variable_name_unsafe = "u" . $element_key;
            if (!isset($this->$variable_name_unsafe))
            {
              foreach($this->prototype as $type => $dataset)
              {
                if (isset($dataset[$element_key]))
                {
                  $column_name = $dataset[$element_key];
                  $this->$variable_name_unsafe = $this->uData[$column_name];
                }
              }
            }
          }
        }
      }

      $element_list = array();
      foreach($this->prototype as $type_key => $type_value)
      {
        foreach($type_value as $element_key => $element_value)
        {
          switch($type_key)
          {
            case "none":
            case "numeric":
            case "boolean":
            case "timestamp":
            case "string":
            case "simplehtml":
            case "html":
            case "nl2br":
              $element_list[$element_value] = array(
                'key'   => $element_key,
                'type'  => $type_key
              );
              break;
            default:
              break;
          }
        }
      }

      $sKeyList = array();
      $sKeyIdentifierList = array();
      $uValueList = array();

      foreach($element_list as $sKey => $value)
      {
        $variable_name_safe = "s" . $value['key'];
        $variable_name_unsafe = "u" . $value['key'];
        if (isset($this->$variable_name_safe) || isset($this->$variable_name_unsafe))
        {
          switch($value['type'])
          {
            case "none":
              $uFinalValue = $this->$variable_name_unsafe;
              break;
            case "numeric":
              $number = (isset($this->$variable_name_unsafe)) ? $this->$variable_name_unsafe : $this->$variable_name_safe;
              $uFinalValue = (is_numeric($number)) ? $number : 0;
              break;
            case "boolean":
              $bool = (isset($this->$variable_name_unsafe)) ? $this->$variable_name_unsafe : $this->$variable_name_safe;
              $uFinalValue = ($bool) ? "1" : "0";
              break;
            case "timestamp":
              if (is_numeric($this->$variable_name_unsafe))
              {
                $uFinalValue = mysql_from_unix($this->$variable_name_unsafe);
              } else
              {
                 if (isset($this->$variable_name_safe))
               {
                 $uFinalValue = mysql_from_unix($this->$variable_name_safe);
                } else
                {
                 $uFinalValue = mysql_from_unix(unix_from_local($this->$variable_name_unsafe));
                }
              }
              break;
            case "string":
            case "simplehtml":
            case "html":
            case "nl2br":
              $uFinalValue = (isset($this->$variable_name_unsafe)) ? $this->$variable_name_unsafe : $this->$variable_name_safe;
              break;
            case "default":
              $uFinalValue = $this->$variable_name_unsafe;
              break;
          }
          $sIdentifier = ":{$sKey}";
          $sKeyList[] = "`{$sKey}`";
          $sKeyIdentifierList[] = $sIdentifier;
          $uValueList[$sIdentifier] = $uFinalValue;
        } else
        {
          if ($this->autoloading === false)
          {
            $classname = get_class($this);
            throw new Exception("Database insertion failed: prototype property {$value['key']} not found in object of type {$classname}.");
          }
        }
      }

      if ($insert_mode == CPHP_INSERTMODE_INSERT)
      {
        $sQueryKeys = implode(", ", $sKeyList);
        $sQueryKeyIdentifiers = implode(", ", $sKeyIdentifierList);
        $query = "INSERT INTO {$this->table_name} ({$sQueryKeys}) VALUES ({$sQueryKeyIdentifiers})";
      } elseif ($insert_mode == CPHP_INSERTMODE_UPDATE)
      {
        $sKeysIdentifiersList = array();
        for($i = 0; $i < count($sKeyList); $i++)
        {
          $sKey = $sKeyList[$i];
          $sValue = $sKeyIdentifierList[$i];
          $sKeysIdentifiersList[] = "{$sKey} = {$sValue}";
        }
        $sQueryKeysIdentifiers = implode(", ", $sKeysIdentifiersList);
        /* We use :CPHPID here because it's unlikely to be used in the application itself. */
        $query = "UPDATE {$this->table_name} SET {$sQueryKeysIdentifiers} WHERE `{$this->id_field}` = :CPHPID";
        $uValueList[':CPHPID'] = $this->sId;
      }

      try
      {
        $result = $database->CachedQuery($query, $uValueList, 0);
        if ($insert_mode == CPHP_INSERTMODE_INSERT)
        {
          $this->sId = $database->lastInsertId();
        }
        $this->RefreshData();
        return $result;
      } catch (DatabaseException $e)
      {
        $classname = get_class($this);
        $error = $database->errorInfo();
        if (empty($error[2]))
        {
          $errmsg = $e->getMessage();
        } else
        {
          $errmsg = $error[2];
        }
        throw new DatabaseException("Database insertion query failed in object of type {$classname}. Error message: " . $errmsg);
      }
    } else
    {
      $classname = get_class($this);
      throw new Exception("No verification query defined for {$classname} class.");
    }
  }

  public function RetrieveChildren($type, $field)
  {
    /* Probably won't ever be fully implemented, now that there is CreateFromQuery. */

    if (!isset($cphp_config->class_map->$type))
    {
      $classname = get_class($this);
      throw new NotFoundException("Non-existent 'type' argument passed on to {$classname}.RetrieveChildren function.");
    }
    
    $parent_type = get_parent_class($cphp_config->class_map->$type);

    if ($parent_type !== "CPHPDatabaseRecordClass")
    {
      $parent_type = ($parent_type === false) ? "NONE" : $parent_type;
      $classname = get_class($this);
      throw new TypeException("{$classname}.RetrieveChildren expected 'type' argument of parent-type CPHPDatabaseRecordClass, but got {$parent_type} instead.");
    }
    $query = "";
  }

  public function PurgeCache()
  {
    $parameters = array(":Id" => (string) $this->sId);
    $query_hash = md5($this->fill_query);
    $parameter_hash = md5(serialize($parameters));
    $cache_hash = $query_hash . $parameter_hash;
    mc_delete($cache_hash);
  }

  public function RenderTemplate($template = "")
  {
    if (!empty($template))
    {
      $this->render_template = $template;
    }
    return $this->DoRenderInternalTemplate();
  }

  public function Export()
  {
    /* This function is DEPRECATED and should not be used. Please manually build your arrays instead. */
    $export_array = array();
    foreach($this->prototype_export as $field)
    {
      $variable_name = "s{$field}";
      if (is_object($this->$variable_name))
      {
        if(!empty($this->$variable_name->sId))
        {
          $export_array[$field] = $this->$variable_name->Export();
        } else
        {
          $export_array[$field] = null;
        }
      } else
      {
        $export_array[$field] = $this->$variable_name;
      }
    }
    return $export_array;
  }

  public static function CreateFromQuery($query, $parameters = array(), $expiry = 0, $first_only = false)
  {
    global $database;
    $result = $database->CachedQuery($query, $parameters, $expiry);
    if($result)
    {
      if ($first_only === true)
      {
        /* TODO: Try to run the query with LIMIT 1 if only the first result is desired. */
        return new static($result);
      } elseif(count($result->data) == 1)
      {
        return array(new static($result));
      } else
      {
        $result_array = array();
        foreach($result->data as $row)
        {
          $result_array[] = new static($row);
        }
        return $result_array;
      }
    } else
    {
      throw new NotFoundException("No results for specified query.");
    }
  }

  // Define events
  protected function EventConstructed() { }
}
