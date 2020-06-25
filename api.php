<?php
require 'Game.php';
require 'Figure.php';

if ($_POST['new'] == 1){
    $game = new Classes\Game();  //Создание нового объекта класса Game
    $answer['RESULT'] = json_encode($game);  //Кодирование всего объекта класса в JSON-строку
    echo $answer;
} else if (is_array($_POST['possibleMoves'])) {
    $game = $_POST['game'];  //Сбор информации о текущей игре из POST-запроса
    $pos_x = $_POST['possibleMoves'][0];
    $pos_y = $_POST['possibleMoves'][1];
    if (is_object($game)) {
        $board = $game->getBoard();
        $answer['RESULT'] = $game->getPossibleMoves($board[$pos_y][$pos_x]);  //Запись возможных ходов в ответ
        echo json_encode($answer);
    } else {
        $answer['RESULT'] = -1;  //Выброс ошибки о пустой доске (игра еще не создана)
        echo json_encode($answer);
    }
} else if (is_array($_POST['move'])){
    $game = $_POST['game'];
    $initial_x = $_POST['move'][0];
    $initial_y = $_POST['move'][1];
    $destination_x = $_POST['move'][2];
    $destination_y = $_POST['move'][3];
    $moveType = $_POST['move'][4];
    $figureType = $_POST['move'][5];
    $board = $game->getBoard;
    $possibleMoves = $game->getPossibleMoves($board[$initial_x][$initial_y]);
    if (is_object($game)) {
        if (sizeof($possibleMoves) > 0) {
            $result = $game->move([$initial_x, $initial_y], $possibleMoves, [$destination_x, $destination_y, $moveType, $figureType]);
            if ($result == -1) {
                $answer['RESULT'] = -2; //Ошибка о неправильном ходе
            } else {
                $answer['RESULT'] = json_encode($result);
            }
            echo $answer;
        } else {
            $answer['ERROR'] = -3; //Ошибка об отсутствии ходов
            echo json_encode($answer);
        }
    } else {
        $answer['RESULT'] = -1;  //Выброс ошибки о пустой доске (игра еще не создана)
        echo json_encode($answer);
    }
} else if ($_POST['reset'] == 1){
    $game = new \Classes\Game();
    $answer['RESULT'] = json_encode($game);
    echo $answer;
} else if ($_POST['getMoveLog'] == 1){
    $game = $_POST['game'];
    if (is_object($game)) {
        $answer['RESULT'] = json_encode($game->getMoveLog());
        echo $answer;
    } else {
        $answer['RESULT'] = -1;  //Выброс ошибки о пустой доске (игра еще не создана)
        echo json_encode($answer);
    }
}
