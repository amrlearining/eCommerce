<?php

    function getTitle(){
        global $pageTitle;

        if (isset($pageTitle)){
            echo $pageTitle;
        } else {
            echo 'Defult';
        }
    }

    // redirect to home v2.0
    function redirectHome ($theMsg, $url=null, $seconds = 3) {

        if ($url === null) {
            $url = 'index.php';
            $link = 'Home Page';
        } else {
            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '') {
                $url = $_SERVER['HTTP_REFERER'];
                $link = 'Previous Page';
            } else {
                $url = 'index.php';
                $link = 'Home Page'; 
            }
        }

        echo $theMsg;
        echo "<div class='alert alert-info'>You will be redirected to $link after $seconds Seconds</div>";
        header("refresh:$seconds;url=$url");
        exit();

    }

    //check item deplicate v1.0
    function checkItem($select, $from, $value) {

        global $con;

        $statment = $con->prepare("SELECT $select FROM $from WHERE $select = ?");

        $statment->execute(array($value));

        $count = $statment->rowCount();

        return $count;
    }

    // count Items v2.0
    function countItem($item, $table, $filter = ";") {
        global $con;
        if ($filter != ";") {
            $filter = "WHERE $item = $filter";
        }

        $stmt = $con->prepare("SELECT COUNT($item) FROM $table $filter");
        $stmt->execute();
        return $stmt->fetchColumn();
    }