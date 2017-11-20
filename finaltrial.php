<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE);
define('DATABASE', 'sjp77');
define('USERNAME', 'sjp77');
define('PASSWORD', 'hS1DY7pYO');
define('CONNECTION', 'sql1.njit.edu');
final1::accs();
final1::accinq();
final1::accins();
final1::accup();
final1::accdel();
final1::display();
final1::todosr();
final1::todoun();
final1::todoins();
final1::todoun();
final1::tododel();
class dbConn
{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
  private function __construct() 
    {
      try
        {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        
     catch (PDOException $e) 
        {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
  public static function getConnection() 
    {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) 
        {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
class collection 
{
 
  protected $html;
  static public function create() 
    {
      $model = new static::$modelName;
      return $model;
    }
  static public function findAll()
    {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
  static public function findOne($id) 
    {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
  static public function display1($records)
    {
       $html = '<table border = 6><tbody>';
       $html .= '<tr>';
      foreach($records[0] as $key=>$value)
         {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
         }
       $html .= '</tr>';
      foreach($records as $key=>$value)
         {
           $html .= '<tr>';
           foreach($value as $key2=>$value2)
             {
               $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
             }
            $html .= '</tr>';
         }
      $html .= '</tbody></table></br><hr>';
      print_r($html);
      return 0;
    }

}

class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model

{
  //-----------------
  protected $tableName;
  public function save()
    {
      if ($this->id != '') 
         {
            $sql = $this->update($this->id);
         } 

      else 
         {
           $sql = $this->insert();
         
         }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
     foreach (array_flip($array) as $key=>$value)
        {
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
  private function insert() 
    {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
 private function update($id)
   {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value)
        {
            if( ! empty($value))
            {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
 public function delete($id)
    {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id>='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    
//---------------------------
class account extends model 
{
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename()
     {
        $tableName='accounts';
        return $tableName;
     }
}
//-----------------------------------
class todo extends model
 {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename()
    {
        $tableName='todos';
        return $tableName;
    }
}
//--------------- Accounts Table-------------------------sjp77
//-------------------------- Find All -------------------sjp77
class final1 extends model
{

 static public function accs()
   {
     echo"<h1>Search accounts table</h1>";
     $records = accounts::findAll();
     $a=accounts::display1($records);
   }
 //--------------------------- Find Unique Record---------------sjp77
 static public function accinq()
  {

    echo"<h1>Search account table by id</h1>";
    $record = accounts::findOne(4);
    $a=accounts::display1($record);
  }
 //-------------------------- Insert Record---------------------sjp77
  static public function accins()
  {
    echo "<h1>Insert One Record</h1>";

    $record = new account();
    $record->email="patel.edu";
    $record->fname="saurabh";
    $record->lname="patel";
    $record->phone="66697";
    $record->birthday="28-12-1991";
    $record->gender="female";
    $record->password="12345";
    $record->save();
    $records = accounts::findAll();
    $a=accounts::display1($records);
  }
  //------------------------- Delete Record -------------------sjp77
  static public function accdel()
     {

      echo "<h1>Delete One Record</h1>";
      $record= new account();
      $id=7;
      $record->delete($id);
      echo '<h3>Record greater then id: '.$id.' is deleted</h3>';
      $record = accounts::findAll();
      $a=accounts::display1($record);
     }

  //-----------------------------Update Record-------------------sjp77
 static public function accup()
     {

      echo "<h1>Update One Record</h1>";
      $id=4;
      $record = new account();
      $record->id=$id;
      $record->fname="saurabh";
      $record->lname="patel";
      $record->gender="male";
      $record->save();
      $record = accounts::findAll();
      echo "<h3>Record update with id: ".$id."</h3>";
      $a=accounts::display1($record);
     }
 //------------------End Of Account Table -----------------------sjp77
 static public  function display()
      {
        echo"<h1><marquee> TODO TABLE FUNCTION STARTS HERE</marquee></h1>";
      }
  //--------------- Todo Table-------------------------sjp77
 static public function todosr()
     {
       echo "<h1>Search all for todo table</h1>";
       $records = todos::findAll();
       $a=todos::display1($records);
     }
 //------------------Find Unique id-------------------sjp77
  static public function todoun()
     {
       echo"<h1>Search by uniqui id</h1>";
       $record = todos::findOne(3);
       $a=todos::display1($record);
     }
  //-------------------------Insert Record-----------------sjp77
 static  public function todoins()
     {
        echo "<h2>Insert One Record</h2>";
        $record = new todo();
        $record->owneremail="sjp77@njit.edu";
        $record->ownerid=06;
        $record->createddate="11-09-2017";
        $record->duedate="11-13-2017";
        $record->message="Active record Assignment";
        $record->isdone=1;
        $record->save();
        $records = todos::findAll();
        echo"<h3>After Inserting</h3>";
        $a=todos::display1($records);
     }
  //------------------------------Delete record for todo ------------------sjp77
  static public function tododel()
     {
        echo "<h1>Delete  Record</h1>";
        $record= new todo();
        $id=7;
        $record->delete($id);
        echo '<h3>Record greater then id: '.$id.' is deleted</h3>';
        $record = todos::findAll();
        $a=todos::display1($record);
     }
  //------------------------Update todos record -------------------------------------------------sjp77
 static public function todoup()
     {
       echo "<h1>Update One Record</h1>";
       $id=4;
       $record = new todo();
       $record->id=$id;
       $record->owneremail="shivangi@gmail.com";
       $record->ownerid="77";
       $record->createddate="01-02-1995";
       $record->duedate="02-01-1995";
       $record->message="hi this is shiv";
       $record->isdone="1";
       $record->save();
       $record = todos::findAll();
     echo "<h3>Record update with id: ".$id."</h3>";
      $a=todos::display1($record);
     }

}
