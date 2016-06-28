<?php

namespace UserData;

class User
{
    private $first_name;
    private $last_name = '';
    private $email;
    private $phone_number = '';
    private $comment = '';

//    public function getId(){
//        return $this->id;
//    }
//
//    public function setId($id){
//        $this->id = $id;
//    }

    public function getFirstName(){
        return $this->first_name;
    }

    public function setFirstName($first_name){
        $this->first_name = $first_name;
    }

    public function getLastName(){
        return $this->last_name;
    }

    public function setLastName($last_name){
        $this->last_name = $last_name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getPhoneNumber(){
        return $this->phone_number;
    }

    public function setPhoneNumber($phone_number){
        $this->phone_number = $phone_number;
    }

    public function getComment(){
        return $this->comment;
    }

    public function setComment($comment){
        $this->comment = $comment;
    }
}