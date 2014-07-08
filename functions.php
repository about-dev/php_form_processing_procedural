<?php
function validation($params = array()){
    $data = array('status'=>'ok', 'message' =>'');
                    
    //name validation
    if(empty($params['first_name'])){
        $data['status']                 = 'error';
        $data['message']['first_name']  = 'Please insert first name!';
    }else{
        $firstName = filter_var($params['first_name'], FILTER_SANITIZE_STRING);
        if(strlen($firstName) < 4){
            $data['status']                 = 'error';
            $data['message']['first_name']  = 'Your first name must be at least 4 characters!';
        }
    }

    //email validation
    if(empty($params['email'])){
        $data['status']             = 'error';
        $data['message']['email']   = 'Please insert your email address!';
    }else{
        $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $data['status']             = 'error';
            $data['message']['email']   = 'Please insert a valid email address!';
        }
    }

    //username validation
    if(empty($params['username'])){
        $data['status']       		= 'error';
        $data['message']['username']= 'Please insert your username!';
    }else{
    	if(strlen($params['username']) < 10){
    		$data['status']       		= 'error';
    		$data['message']['username']= 'Your username must have at least 10 characters!';
    	}
    }

    //password validation
    if(empty($params['password'])){
        $data['status']             = 'error';
        $data['message']['password']= 'Please insert your password!';
    }else{
        if($params['password'] != $params['confirm_password']){
            $data['status']             = 'error';
            $data['message']['password']= 'Your passwords must match!';
        }
    }

    //address validation
    if(empty($params['address'])){
        $data['status']             = 'error';
        $data['message']['address'] = 'Please insert your address!';
    }

    return $data;
}

function save($params = array()){
    $data = array('status'=>'ok', 'message' =>'');
    $conn = mysqli_connect('localhost', 'root', '', 'test');
    if(!$conn){
        //brut error message: mysqli_connect_error()
        $data['status']         = 'error';
        $data['message']['db']  = 'Something went wrong. Please try again later!';
    }

    unset($params['confirm_password']);

    if(!empty($params)){
        //if user already exists
        $selectSql = '  SELECT * 
                        FROM users 
                        WHERE email="'.mysqli_real_escape_string($conn, $params['email']).'"';
        $exists = mysqli_num_rows(mysqli_query($conn, $selectSql));
        if($exists == 0){
            $sql = 'INSERT INTO users SET ';
        }else{
            $sql = 'UPDATE users SET ';
        }

        //provide some mininum checks
        foreach($params as $name=>$value){
            $params[$name] = trim(strip_tags($value));
            $sql .= $name.'="'.mysqli_real_escape_string($conn, addslashes($value)).'", ';
        }

        $sql = substr($sql, 0, -2);
        if($exists){
            $sql .= 'WHERE email="'.mysqli_real_escape_string($conn, $params['email']).'"';
        }

        mysqli_query($conn, $sql);
        $dbErrors = mysqli_error_list($conn);
        if(empty($dbErrors) && mysqli_affected_rows($conn)){
            $data['status']             = 'ok';
            $data['message']['success'] = 'Thank you!';
        }else{
            $data['status']         = 'error';
            $data['message']['db']  = 'Something went wrong. Please try again later!';
        }
    }

    return $data;
}
?>