<?php

if(!array_key_exists('path', $_GET)){
    echo "Missing page.";
    exit;
}

$path = explode('/', $_GET['path']);
$contents = file_get_contents('db.json');

$json = json_decode($contents, 1);
$body = file_get_contents('php://input');
$busca = false;

if(empty($_GET['path']) or !array_key_exists($path[0], $json)){
    echo "Missing page.";
    exit;
}


$method = $_SERVER['REQUEST_METHOD'];

header('Content-type: application/json');

if($method === 'GET'){
    if(count($path) <= 1){
        echo json_encode($json[$path[0]]);
    }else{
        foreach($json[$path[0]] as $key => $values){
            if($key == $path[1]){
                echo json_encode($json[$path[0]][$path[1]]);
                $busca = true;
            }
        }
        if(!$busca){
            echo "Missing page.";
            exit;
        }
        
    }
}


if($method === 'POST'){
    $jsonBody = json_decode($body, 1);
    $jsonBody['id'] = "".time()."";
    if(!$json[$path[0]]){
       $json[$path[0]] = [];
    }
    array_push($json[$path[0]], $jsonBody);
    echo "Posted => ".json_encode($jsonBody);
    file_put_contents('db.json', json_encode($json));
}

if($method === 'DELETE'){
    if(count($path) <= 1){
        echo 'Unexpected way.';
    }else{
        foreach($json[$path[0]] as $key => $values){
            if($key == $path[1]){
                echo "Deleted => ".json_encode($json[$path[0]][$path[1]]);
                $busca = true;
            }
        }
        if(!$busca){
            echo "Unexpected way.";
            exit;
        }
        unset($json[$path[0]][$path[1]]);
        file_put_contents('db.json', json_encode($json));        
    }
}

if($method === 'PUT'){
    if(count($path) <= 1){
        echo 'Missing page.';
    }else{
        foreach($json[$path[0]] as $key => $values){
            if($key == $path[1]){
                $busca = true;
            }
        }
        if(!$busca){
            echo "Missing page.";
            exit;
        }
        $jsonBody = json_decode($body, 1);
        $jsonBody['id'] = $json[$path[0]][$path[1]]['id'];
        $json[$path[0]][$path[1]] = $jsonBody;
        echo "Updated => ".json_encode($json[$path[0]][$path[1]]);
        file_put_contents('db.json', json_encode($json));        
    }
}