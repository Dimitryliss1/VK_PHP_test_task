<?php

$moveLog = array();
$turnNumber = 0;
$board = Null;  //В доске первая координата относится к y, вторая -- к x. Формат -- board[y][x]

const ERROR = -1;
const NO_WARNINGS = 0;
const BLACKWIN = 1;
const WHITEWIN = 2;

function start_new_game(){
    global $board;
    $board = array_fill(1, 8, array_fill(1, 8, 0));  //Массив, содержащий сведения о доске
    //Если в клетке нет фигуры, то в ней 0, иначе -- фигура со всеми ее параметрами
    for ($j = 1; $j <= 2; $j++){
        for ($i = 1; $i <= 8; $i++){
            $figure = new Classes\Figure($i, $j);  //Создание фигуры по ее координатам
            $board[$j][$i] = $figure;
        }
    }
    for ($j = 7; $j <= 8; $j++){
        for ($i = 1; $i <= 8; $i++){
            $figure = new Classes\Figure($i, $j);
            $board[$j][$i] = $figure;
        }
    }
//    echo "Game\'s ready for you! First turn is for white"; Необязательно
    global $turnNumber;
    $turnNumber = 1;
}

function getPossibleFigures(int $turnNumber){  //Возвращает возможные фигуры для конкретного игрока
    $possibleFigures = array();
    global $board;
    if ($turnNumber % 2 == 1){  //Для белых
        foreach ($board as $row){
            foreach ($row as $tile){
                if ($tile == 0){
                    continue;
                } else {
                    if ($tile->getColor() == 'w'){
                        $possibleFigures[] = $tile; //Если в клетке есть фигура, и она того цвета, который нужен, то закидываем
                    }
                }
            }
        }
    } else {  //Для черных
        foreach ($board as $row){
            foreach ($row as $tile){
                if ($tile == 0){
                    continue;
                } else {
                    if ($tile->getColor() == 'b'){
                        $possibleFigures[] = $tile; //Если в клетке есть фигура, и она того цвета, который нужен, то закидываем
                    }
                }
            }
        }
    }
    return $possibleFigures;
}

function checkForKing(string $color){  //Функция проверяет на наличие короля нужного цвета на доске
    global $board;
    foreach ($board as $row){
        foreach ($row as $tile){
            if ($tile == 0){
                continue;  //Пропуск пустой клетки
            } else {
                if ($tile->getType() == 'k' && $tile->getColor() == $color){
                    return True; //Король есть
                }
            }
        }
    }
    return False;  //Короля нет
}

function getPossibleMoves(\Classes\Figure $figure){  //Определение возможных ходов для заданной фигуры
    $possibleMoves = array();
    global $board;
    $pos_X = $figure->getPositionX();
    $pos_Y = $figure->getPositionY();
    if ($figure->getType() == 'p') {  //Логика ходьбы для пешки
        if ($board[$pos_Y + 1][$pos_X + 1] != 0 && $board[$pos_Y + 1][$pos_X + 1]->getColor() != $figure->getColor()) {
            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1];
        } else if ($board[$pos_Y - 1][$pos_X - 1] != 0 && $board[$pos_Y - 1][$pos_X - 1]->getColor() != $figure->getColor()) {
            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1];
        } else if ($board[$pos_Y + 1][$pos_X - 1] != 0 && $board[$pos_Y + 1][$pos_X - 1]->getColor() != $figure->getColor()) {
            $possibleMoves[] = [$pos_X - 1, $pos_Y + 1];
        } else if ($board[$pos_Y - 1][$pos_X + 1] != 0 && $board[$pos_Y - 1][$pos_X + 1]->getColor() != $figure->getColor()) {
            $possibleMoves[] = [$pos_X + 1, $pos_Y - 1];
        }

        if ($figure->getColor() == 'w') {
            if ($board[$pos_Y + 1][$pos_X] == 0) {
                $possibleMoves[] = [$pos_X, $pos_Y + 1];
                if ($figure->getMoveCounter() == 0 && $board[$pos_Y + 2][$pos_X] == 0) {
                    $possibleMoves[] = [$pos_X, $pos_Y + 2];
                }
            }
        } else if ($figure->getColor() == 'b') {
            if ($board[$pos_Y - 1][$pos_X] == 0) {
                $possibleMoves[] = [$pos_X, $pos_Y - 1];
                if ($figure->getMoveCounter() == 0 && $board[$pos_Y - 2][$pos_X] == 0) {
                    $possibleMoves[] = [$pos_X, $pos_Y - 2];
                }
            }
        }
    } else if ($figure->getType() == 'r'){   //Логика ходов для ладьи
        for ($i = $pos_X - 1; $i > 0; $i--){
            if ($board[$pos_Y][$i] == 0 || $board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$i, $pos_Y];
                if ($board[$pos_Y][$i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = $pos_X + 1; $i < 9; $i++){
            if ($board[$pos_Y][$i] == 0 || $board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$i, $pos_Y];
                if ($board[$pos_Y][$i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($j = $pos_Y - 1; $j > 0; $j--){
            if ($board[$j][$pos_X] == 0 || $board[$j][$pos_X]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X, $j];
                if ($board[$j][$pos_X] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($j = $pos_Y + 1; $j < 9; $j++){
            if ($board[$j][$pos_X] == 0 || $board[$j][$pos_X]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X, $j];
                if ($board[$j][$pos_X] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
    } else if ($figure->getType() == 'b'){  //Логика ходов для слона
        for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++){
            if ($board[$pos_Y + $i][$pos_X + $i] == 0 || $board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X + $i, $pos_Y + $i];
                if ($board[$pos_Y + $i][$pos_X + $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++){
            if ($board[$pos_Y + $i][$pos_X - $i] == 0 || $board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X + $i, $pos_Y - $i];
                if ($board[$pos_Y + $i][$pos_X - $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++){
            if ($board[$pos_Y - $i][$pos_X + $i] == 0 || $board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X - $i, $pos_Y + $i];
                if ($board[$pos_Y - $i][$pos_X + $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++){
            if ($board[$pos_Y - $i][$pos_X - $i] == 0 || $board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X - $i, $pos_Y - $i];
                if ($board[$pos_Y - $i][$pos_X - $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
    } else if ($figure->getType() == 'n') {  //Логика ходов для коня
        $possible_changes_1 = [-1, 1];
        $possible_changes_2 = [-2, 2];
        foreach ($possible_changes_1 as $i) {
            foreach ($possible_changes_2 as $j) {
                if ($board[$pos_Y + $j][$pos_X + $i] == 0 || $board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y + $j];
                }
            }
        }
        foreach ($possible_changes_2 as $i) {
            foreach ($possible_changes_1 as $j) {
                if ($board[$pos_Y + $j][$pos_X + $i] == 0 || $board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y + $j];
                }
            }
        }
    } else if ($figure->getType() == 'k'){  //Логика ходов для короля
        $possible_changes = [-1, 0, 1];
        foreach ($possible_changes as $i){
            foreach ($possible_changes as $j){
                if ($i == 0 && $j == 0){
                    continue;
                } else {
                    if ($board[$pos_Y + $j][$pos_X+$i] == 0 || $board[$pos_Y + $j][$pos_X+$i]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $j];
                    }
                }
            }
        }
    } else if ($figure->getType() == 'q'){  //Логика ходов для ферзя
        for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++){
            if ($board[$pos_Y + $i][$pos_X + $i] == 0 || $board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X + $i, $pos_Y + $i];
                if ($board[$pos_Y + $i][$pos_X + $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++){
            if ($board[$pos_Y + $i][$pos_X - $i] == 0 || $board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X + $i, $pos_Y - $i];
                if ($board[$pos_Y + $i][$pos_X - $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++){
            if ($board[$pos_Y - $i][$pos_X + $i] == 0 || $board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X - $i, $pos_Y + $i];
                if ($board[$pos_Y - $i][$pos_X + $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++){
            if ($board[$pos_Y - $i][$pos_X - $i] == 0 || $board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X - $i, $pos_Y - $i];
                if ($board[$pos_Y - $i][$pos_X - $i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = $pos_X - 1; $i > 0; $i--){
            if ($board[$pos_Y][$i] == 0 || $board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$i, $pos_Y];
                if ($board[$pos_Y][$i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($i = $pos_X + 1; $i < 9; $i++){
            if ($board[$pos_Y][$i] == 0 || $board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$i, $pos_Y];
                if ($board[$pos_Y][$i] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($j = $pos_Y - 1; $j > 0; $j--){
            if ($board[$j][$pos_X] == 0 || $board[$j][$pos_X]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X, $j];
                if ($board[$j][$pos_X] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
        for ($j = $pos_Y + 1; $j < 9; $j++){
            if ($board[$j][$pos_X] == 0 || $board[$j][$pos_X]->getColor() != $figure->getColor()) {
                $possibleMoves[] = [$pos_X, $j];
                if ($board[$j][$pos_X] != 0) {
                    break;
                }
            } else {
                break;
            }
        }
    }
    return $possibleMoves;
}

function move(\Classes\Figure $figure, array $possibleMoves, array $destination){  //Собственно, функция движения
    global $board;
    global $turnNumber;
    global $moveLog;
    if (in_array($destination, $possibleMoves) == False){
        return ERROR;
    } else {
        $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], ];
        $board[$destination[1]][$destination[0]] = $figure;
        $figure->setMoveCounter(1);
        $board[$figure->getPositionY()][$figure->getPositionX()] = 0;
        $figure->setPositionY($destination[1]);
        $figure->setPositionX($destination[0]);
        $turnNumber += 1;

        if ($turnNumber % 2 == 1) {  //Для белых
            if (checkForKing('w') == False){
                return BLACKWIN;
            }
        } else {
            if (checkForKing('b') == False){
                return WHITEWIN;
            }
        }
    }
}