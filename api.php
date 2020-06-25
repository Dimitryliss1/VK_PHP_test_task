<?php
$board = Null;
$possibleMoves = Null;
require 'Game.php';
require 'Figure.php';

if ($_GET['new'] == 1){
    global $board;
    $board = new \Classes\Game();
    $answer = json_encode($board->getBoard());
    echo $answer;
}

if (is_array($_GET['possibleMoves'])){
    global $board;
    $pos_x = $_GET['PossibleMoves'][0];
    $pos_y = $_GET['PossibleMoves'][1];
    if (is_object($board)) {
        $answer = $board->getPossibleMoves($board->getBoard()[$pos_y][$pos_x]);
        global $possibleMoves;
        $possibleMoves = $answer;
        echo json_encode($answer);
    } else {
        $answer['ERROR'] = -1;
        echo json_encode($answer);
    }
}

if (is_array($_GET['move'])){
    global $board;
    $initial_x = $_GET['move'][0];
    $initial_y = $_GET['move'][1];
    $destination_x = $_GET['move'][2];
    $destination_y = $_GET['move'][3];
    $moveType = $_GET['move'][4];
    $figureType = $_GET['move'][5];
    global $possibleMoves;
    if (is_array($possibleMoves)){
        $answer = $board->move([$initial_x, $initial_y], $possibleMoves, [$destination_x, $destination_y, $moveType, $figureType]);
        $answer = json_encode($answer);
        echo $answer;
    } else {
        $answer['ERROR'] = -1;
        echo json_encode($answer);
    }
}

if ($_GET['reset'] == 1){
    global $board;
    $board = null;
    $answer = json_encode($board->getBoard());
    echo $answer;
}

if ($_GET['getMoveLog'] == 1){
    global $board;
    $answer = json_encode($board->getMoveLog());
    echo $answer;
}
