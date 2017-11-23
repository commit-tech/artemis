<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
include_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');

class UserController {
    private static $instance;
    private $userList;

    private function __construct() {
        $this->userList = new DBOperation("users");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addUser($user, $password) {
    	$name = $user->getName();
    	$matricNumber = $user->getMatricNumber();
    	$hashedpassword = sha1($password);
    	$contact = $user->getContact();
    	$email = $user->getEmail();
    	$position = $user->getPosition();
    	$cell = $user->getCell();

        try{
            $conn = connect();
            if(!self::isUserExist($user, $conn)){
                $stmt = $conn->prepare('INSERT INTO users (name, password, email, matric_number, contact, cell, position) VALUES (:name, :password, :email, :matric_number, :contact, :cell, :position)');
                $stmt->execute(array('name' => $name,
                                    'password' => $hashedpassword,
                                    'email' => $email,
                                    'matric_number' => $matricNumber,
                                    'contact' => $contact,
                                    'cell' => $cell,
                                    'position' => $position));

                if($stmt->rowCount() != 1){
                    die("User insertion error");
                }
                //stub: send email
                $conn = null;
                return true;
            }
            return false;
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function removeUser($userID) {
        try{
            $conn = connect();
            $stmt = $conn->prepare('DELETE FROM users WHERE id = :userID');
            $stmt->execute(array('userID' => $userID));
            if($stmt->rowCount() != 1){
                die("User deletion error");
            }
            $conn = null;
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function editUser($id, $param){
        try{
            $conn = connect();
            if($param['pass'] != "no"){
                $stmt = $conn->prepare("UPDATE users SET email = :email, password = :password, contact = :contact, notification = :notification, cell = :cell, position = :position WHERE id = :id");
                $stmt->execute(array('id'=>$id,
                                     'email'=>$param['email'],
                                     'password'=>$param['pass'],
                                     'contact'=>$param['contact'],
                                     'notification'=>$param['subscribe'],
                                     'cell'=>$param['cell'],
                                     'position'=>$param['position']));
            }
            else{
                $stmt = $conn->prepare("UPDATE users SET email = :email, contact = :contact, notification = :notification, cell = :cell, position = :position WHERE id = :id");
                $stmt->execute(array('id'=>$id,
                                     'email'=>$param['email'],
                                     'contact'=>$param['contact'],
                                     'notification'=>$param['subscribe'],
                                     'cell'=>$param['cell'],
                                     'position'=>$param['position']));
            }
            /*if($stmt->rowCount() != 1){
                    die("Update profile error");
            }*/
            return 1;
        } catch (PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
    }

    /*public function getAllUser(){
        try{
            $conn = connect();
            $all_users = $conn->query('SELECT name, matric_number, email, contact, cell, position FROM users');
            $conn = null;
            return $all_users;
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }*/

    public function getAllUser(){
        try{
            $all_users = $this->userList->getQuery('SELECT id, name, matric_number, email, contact, cell, position, status FROM users');
            return $all_users;
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function getAllActiveSubcoms() {
        try{
            $queryCondition = array('status' => 1,
                                    'position' => 'Subcom',
                                    'duty' => 1);
            return $this->userList->get($queryCondition);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function getAllActiveMembers() {
        try{
            $queryCondition = array('status' => 1,
                                    'duty' => 1,);
            return $this->userList->get($queryCondition);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function getAllActiveMCs() {
        try{
            return $this->userList->getQuery('SELECT * FROM users WHERE status = "1" AND duty = "1" AND position != "Subcom"');
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function getAllActiveUser() {
        try{
            $queryCondition = array('status' => 1);
            return $this->userList->get($queryCondition);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    //check if there is such user in the database (based on matricnumber)
    private function isUserExist($user, $conn){
        $stmt = $conn->prepare('SELECT count(*) FROM users WHERE matric_number = :matricNumber');
        $stmt->execute(array('matricNumber' => $user->getMatricNumber()));

        return $stmt->fetchColumn();
    }

    public function getUserName($userID) {
        if ($userID == -1) return "Drop";
        if ($userID == 0) return "NO_DUTY";
        $condition = array();
        $condition["id"] = $userID;
        $user = $this->userList->get($condition);
        return $user[0]["name"];
    }

    public function getCell($userID) {
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        return $user[0]["cell"];
    }

    public function isAdmin($userID) {
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        return $user[0]["is_admin"];
    }

    public function isMC($userID) {
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        return $user[0]["position"] != "Subcom";
    }

    public function getTrackingStatus($userID) {
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        return $user[0]["tracking"];
    }

    public function getDutyStatus($userID) {
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        return $user[0]["duty"];
    }

    public function getUserInfo($id){
        try{
            $conn = connect();
            $stmt = $conn->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
            $stmt->execute(array('id'=>$id));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();
        }
    }

    public function changeUserStatus($userID){
        $condition = array('id' => $userID);
        $user = $this->userList->get($condition);
        $current_status = $user[0]["status"];
        $new_status = $current_status ^ 1;

        $updateValue = array('status' => $new_status);
        $this->userList->updateData($condition, $updateValue);
    }

    public function resetPassword($userID, $newPassword){
        $condition = array('id' => $userID);
        $updateValue = array('password' => sha1($newPassword));
        $this->userList->updateData($condition, $updateValue);
    }

    public function getNotifyUsers(){
        $condition = array('status' => 1,
                           'notification' => 1);
        return $this->userList->get($condition);
    }

    public function getUserByMatric($matricnumber){
        $condition = array('matric_number' => $matricnumber);
        $user = $this->userList->get($condition);
        return $user[0];
    }

    public function getEODTargets(){
        $condition = array('cell' => 'Center and Ops');
         return $this->userList->get($condition);
    }

    public function getProblemReportTargets(){
        $condition = array('cell' => 'Technical');
         return $this->userList->get($condition);
    }
}
?>
