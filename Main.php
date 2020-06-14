<?php

$moveLog = array();
$turnNumber = 0;
$board = Null;

function start_new_game(){
    global $board;
    $board = array_fill(1, 8, array_fill(1, 8, 0));  //Массив, содержащий сведения о доске
    //Если в клетке нет фигуры, то в ней 0, иначе -- фигура со всеми ее параметрами
    for ($j = 1; $j <= 2; $j++){
        for ($i = 1; $i <= 8; $i++){
            $figure = new Classes\Figure($i, $j);  //Создание фигуры по ее координатам
            $board[$j][$i] = [$figure, [$i, $j], $figure->getType(), $figure->getColor(), 0];
        }
    }
    for ($j = 7; $j <= 8; $j++){
        for ($i = 1; $i <= 8; $i++){
            $figure = new Classes\Figure($i, $j);
            $board[$j][$i] = [$figure, [$i, $j], $figure->getType(), $figure->getColor(), 0];
        }
    }
    echo "Game\'s ready for you! First turn is for white";
    global $turnNumber;
    $turnNumber = 1;
}

function getPossibleFigures($board, $turnNumber){  //Возвращает возможные фигуры для конкретного игрока
    $possibleFigures = array();
    if ($turnNumber % 2 == 1){  //Для белых
        foreach ($board as $row){
            foreach ($row as $tile){
                if ($tile == 0){
                    continue;
                } else {
                    if ($tile[3] == 'w'){
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
                    if ($tile[3] == 'b'){
                        $possibleFigures[] = $tile; //Если в клетке есть фигура, и она того цвета, который нужен, то закидываем
                    }
                }
            }
        }
    }
    return $possibleFigures;
}

function checkForKing($board, $color){  //Функция проверяет на наличие короля нужного цвета на доске
    foreach ($board as $row){
        foreach ($row as $tile){
            if ($tile == 0){
                continue;  //Пропуск пустой клетки
            } else {
                if ($tile[2] == 'k' && $tile[3] == $color){
                    return True; //Король есть
                }
            }
        }
    }
    return False;  //Короля нет
}

function getPossibleMoves($figure){  //Определение возможных ходов для заданной фигуры
    $possibleMoves = array()
    if ($figure[2] == 'p' && $figure[4] == 0){
        $possibleMoves[] =
    }
}

