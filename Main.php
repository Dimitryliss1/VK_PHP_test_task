<?php

//Формат лога движений -- изначальная пара координат X, Y, затем координаты назначения, затем цвет фигуры.
$moveLog = array();
$turnNumber = 0;
$board = Null;  //В доске первая координата относится к y, вторая -- к x. Формат -- board[y][x]
$roqueWhite = 0;
$roqueBlack = 0;
const ERROR = -1;
const NO_WARNINGS = 0;
const BLACKWIN = 1;
const WHITEWIN = 2;

require 'Figure.php';

function start_new_game()
{
    global $board;
    $board = Null;
    $board = array_fill(1, 8, array_fill(1, 8, 0));  //Массив, содержащий сведения о доске
    //Если в клетке нет фигуры, то в ней 0, иначе -- фигура со всеми ее параметрами
    for ($j = 1; $j <= 2; $j++) {
        for ($i = 1; $i <= 8; $i++) {
            $figure = new Classes\Figure($i, $j);  //Создание фигуры по ее координатам
            $board[$j][$i] = $figure; //Запись в массив доски фигуры
        }
    }
    for ($j = 7; $j <= 8; $j++) { //Та же история, только с другим цветом
        for ($i = 1; $i <= 8; $i++) {
            $figure = new Classes\Figure($i, $j);
            $board[$j][$i] = $figure;
        }
    }
//    echo "Game\'s ready for you! First turn is for white"; Необязательно
    global $turnNumber;
    $turnNumber = 1;
    global $roqueBlack;
    global $roqueWhite;
    $roqueWhite = 0;
    $roqueBlack = 0; // Обнуление параметров
    return NO_WARNINGS;
}

function getPossibleFigures(int $turnNumber)
{  //Возвращает возможные фигуры для конкретного игрока
    $possibleFigures = array(); //Массив доступных фигур
    global $board;
    if ($turnNumber % 2 == 1) {  //Для белых
        foreach ($board as $row) {
            foreach ($row as $tile) {
                if (is_int($tile)) {
                    continue;
                } else {
                    if ($tile->getColor() == 'w') {
                        $possibleFigures[] = $tile; //Если в клетке есть фигура, и она того цвета, который нужен, то закидываем
                    }
                }
            }
        }
    } else {  //Для черных
        foreach ($board as $row) {
            foreach ($row as $tile) {
                if (is_int($tile)) {
                    continue;
                } else {
                    if ($tile->getColor() == 'b') {
                        $possibleFigures[] = $tile; //Если в клетке есть фигура, и она того цвета, который нужен, то закидываем
                    }
                }
            }
        }
    }
    return $possibleFigures;
}

function checkForKing(string $color)
{  //Функция проверяет на наличие короля нужного цвета на доске
    global $board;
    foreach ($board as $row) {
        foreach ($row as $tile) {
            if (is_int($tile)) {
                continue;  //Пропуск пустой клетки
            } else {
                if ($tile->getType() == 'k' && $tile->getColor() == $color) {
                    return True; //Король есть
                }
            }
        }
    }
    return False;  //Короля нет
}

function getPossibleMoves(\Classes\Figure $figure)
{  //Определение возможных ходов для заданной фигуры
    /** Формат элемента массива возможных ходов
     *  Сначала две координаты назначения, затем тип хода ('m' -- простой, 'r' -- рокировка), затем -- код фигуры, в которую превратится данная фигура после хода (для смены типа пешкой)
     **/
    $possibleMoves = array();
    global $board;
    $pos_X = $figure->getPositionX(); //Заполнение координат X и Y
    $pos_Y = $figure->getPositionY();
    if ($figure->getType() == 'p') {  //Логика ходьбы для пешки
        if ($figure->getColor() == 'w') {
            if ($pos_X != 8 && is_object($board[$pos_Y + 1][$pos_X + 1])) {
                if ($board[$pos_Y + 1][$pos_X + 1]->getColor() != $figure->getColor()) {
                    if ($pos_Y + 1 == 8) {
                        $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'r'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'q'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'n'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'b'];  //Механизм смены типа пешки при достижении края доски
                    } else {
                        $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'p'];  //Обычный ход
                    }
                }
            } else if ($pos_X != 1 && is_object($board[$pos_Y + 1][$pos_X - 1])){
                if ($board[$pos_Y + 1][$pos_X - 1]->getColor() != $figure->getColor()) {
                    if ($pos_Y + 1 == 8) {
                        $possibleMoves[] = [$pos_X - 1, $pos_Y + 1, 'm', 'r'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y + 1, 'm', 'q'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y + 1, 'm', 'n'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y + 1, 'm', 'b'];
                    } else {
                        $possibleMoves[] = [$pos_X - 1, $pos_Y + 1, 'm', 'p'];
                    }
                }
            }
            if ($pos_Y + 1 == 8) {
                $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'r'];
                $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'q'];
                $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'n'];
                $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'b']; //Движение вперед со сменой типа (край доски)
            } else if ($pos_Y + 1 < 8 && is_int($board[$pos_Y + 1][$pos_X])) {
                $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'p']; //Движение вперед обычное
                if ($figure->getMoveCounter() == 0 && is_int($board[$pos_Y + 2][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $pos_Y + 2, 'm', 'p']; //Первый ход пешки
                }
            }
        } else if ($figure->getColor() == 'b') {
            if ($pos_X != 1 && is_object($board[$pos_Y - 1][$pos_X - 1])){
                if ($board[$pos_Y - 1][$pos_X - 1]->getColor() != $figure->getColor()) {
                    if ($pos_Y - 1 == 1) {
                        $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'r'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'q'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'n'];
                        $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'b'];
                    } else {
                        $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'p'];
                    }
                }
            } else if ($pos_X != 8 && is_object($board[$pos_Y - 1][$pos_X + 1])){
                if ($board[$pos_Y - 1][$pos_X + 1]->getColor() != $figure->getColor()) {
                    if ($pos_Y - 1 == 1) {
                        $possibleMoves[] = [$pos_X + 1, $pos_Y - 1, 'm', 'r'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y - 1, 'm', 'q'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y - 1, 'm', 'n'];
                        $possibleMoves[] = [$pos_X + 1, $pos_Y - 1, 'm', 'b'];
                    } else {
                        $possibleMoves[] = [$pos_X + 1, $pos_Y - 1, 'm', 'p'];
                    }
                }
            }
            if ($pos_Y - 1 == 1) {
                $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'r'];
                $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'q'];
                $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'n'];
                $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'b'];
            } else if ($pos_Y - 1 > 1 && is_int($board[$pos_Y - 1][$pos_X])) {
                $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'p'];
                if ($figure->getMoveCounter() == 0 && is_int($board[$pos_Y - 2][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $pos_Y - 2, 'm', 'p'];
                }
            }
        }
    } else if ($figure->getType() == 'r') {   //Логика ходов для ладьи  //TODO: Переписать все, что ниже
        for ($i = $pos_X - 1; $i > 0; $i--) {
            if (is_int($board[$pos_Y][$i])) { //Ищем ходы в каждую сторону, пока не натыкаемся на врага или своего. Если враг, то его клетку записываем, иначе -- нет
                $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
            } else {
                if ($board[$pos_Y][$i]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                }
                break;
            }
        }
        for ($i = $pos_X + 1; $i < 9; $i++) {
            if (is_int($board[$pos_Y][$i])) {
                $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
            } else {
                if ($board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                }
                break;
            }
        }
        for ($j = $pos_Y - 1; $j > 0; $j--) {
            if (is_int($board[$j][$pos_X])) {
                $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
            } else {
                if ($board[$j][$pos_X]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                }
                break;
            }
        }
        for ($j = $pos_Y + 1; $j < 9; $j++) {
            if (is_int($board[$j][$pos_X])) {
                $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
            } else {
                if ($board[$j][$pos_X]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                }
                break;
            }
        }
    } else if ($figure->getType() == 'b') {  //Логика ходов для слона
        for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++) {  //Так, как и для ладьи. Ищем ходы до тех пор, пока клетка или свободна, или в ней враг другого цвета.
            if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                if (is_int($board[$pos_Y + $i][$pos_X + $i])) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++) {
            if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                if (is_int($board[$pos_Y + $i][$pos_X - $i])) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++) {
            if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                if (is_int($board[$pos_Y - $i][$pos_X + $i])) {
                    $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++) {
            if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                if (is_int($board[$pos_Y - $i][$pos_X - $i])) {
                    $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
    } else if ($figure->getType() == 'n') {  //Логика ходов для коня
        $possible_changes_1 = [-1, 1];
        $possible_changes_2 = [-2, 2];
        foreach ($possible_changes_1 as $i) { //Просто перебор всех комбинаций вида (+-1, +-2)
            foreach ($possible_changes_2 as $j) {
                if (1 <= $pos_X + $i && $pos_X + $i <= 8 && 1 <= $pos_Y + $j && $pos_Y + $j <= 8) {
                    if (is_int($board[$pos_Y + $j][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                    } else {
                        if ($board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                        }
                    }
                }
            }
        }
        foreach ($possible_changes_2 as $i) {  //Просто перебор всех комбинаций вида (+-2, +-1)
            foreach ($possible_changes_1 as $j) {
                if (1 <= $pos_X + $i && $pos_X + $i <= 8 && 1 <= $pos_Y + $j && $pos_Y + $j <= 8) {
                    if (is_int($board[$pos_Y + $j][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                    } else {
                        if ($board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                        }
                    }
                }
            }
        }
    } else if ($figure->getType() == 'k') {  //Логика ходов для короля. Рокировка пока только через него
        $possible_changes = [-1, 0, 1]; //Перебор всех 9 комбинаций
        foreach ($possible_changes as $i) {
            foreach ($possible_changes as $j) {
                if ($i == 0 && $j == 0) {
                    continue;
                } else {
                    if (1 <= $pos_X + $i && $pos_X + $i <= 8 && 1 <= $pos_Y + $j && $pos_Y + $j <= 8) {
                        if (is_int($board[$pos_Y + $j][$pos_X + $i])) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'k'];
                        } else {
                            if ($board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                                $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'k'];
                            }
                        }
                    }
                }
            }
        }
        global $roqueWhite; //Была ли совершена рокировка игроком соответствующего цвета (переменные инициализируются в начале игры)
        global $roqueBlack;
        if ($pos_Y == 1) {
            if ($figure->getMoveCounter() == 0 && $roqueWhite == 0) {
                if (is_object($board[$pos_Y][1])) {
                    if ($board[$pos_Y][1]->getMoveCounter() != 0) {
                        $broken = False;
                        for ($i = 2; $i < $pos_X; $i++) {  //Проверяем, пустые ли клетки
                            if (is_int($board[$pos_Y][$i])) {
                                continue;
                            } else {
                                $broken = True;
                                break;
                            }
                        }
                        if ($broken == False) {
                            $possibleMoves[] = [$pos_X - 2, $pos_Y, 'r', 'k'];
                        }
                    }
                }
                if (is_object($board[$pos_Y][8])) {  //Проверяем, пустые ли клетки в другую сторону
                    if ($board[$pos_Y][8]->getMoveCounter() != 0) {
                        $broken = False;
                        for ($i = 8; $i > $pos_X; $i--) {
                            if (is_int($board[$pos_Y][$i])) {
                                continue;
                            } else {
                                $broken = True;
                                break;
                            }
                        }
                        if ($broken == False) {
                            $possibleMoves[] = [$pos_X + 2, $pos_Y, 'r', 'k'];
                        }
                    }
                }
            }
        } else {
            if ($figure->getMoveCounter() == 0 && $roqueBlack == 0) {
                if (is_object($board[$pos_Y][1])) {
                    if ($board[$pos_Y][1]->getMoveCounter() != 0) {
                        $broken = False;
                        for ($i = 2; $i < $pos_X; $i++) {  //Проверяем, пустые ли клетки
                            if (is_int($board[$pos_Y][$i])) {
                                continue;
                            } else {
                                $broken = True;
                                break;
                            }
                        }
                        if ($broken == False) {
                            $possibleMoves[] = [$pos_X - 2, $pos_Y, 'r', 'k'];
                        }
                    }
                }
                if (is_object($board[$pos_Y][8])) {  //Проверяем, пустые ли клетки в другую сторону
                    if ($board[$pos_Y][8]->getMoveCounter() != 0) {
                        $broken = False;
                        for ($i = 8; $i > $pos_X; $i--) {
                            if (is_int($board[$pos_Y][$i])) {
                                continue;
                            } else {
                                $broken = True;
                                break;
                            }
                        }
                        if ($broken == False) {
                            $possibleMoves[] = [$pos_X + 2, $pos_Y, 'r', 'k'];
                        }
                    }
                }
            }
        }
    } else if ($figure->getType() == 'q') {//Логика ходов для ферзя. Сочетание поиска ферзем и слоном.
        for ($i = $pos_X - 1; $i > 0; $i--) {
            if (is_int($board[$pos_Y][$i])) { //Ищем ходы в каждую сторону, пока не натыкаемся на врага или своего. Если враг, то его клетку записываем, иначе -- нет
                $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
            } else {
                if ($board[$pos_Y][$i]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                }
                break;
            }
        }
        for ($i = $pos_X + 1; $i < 9; $i++) {
            if (is_int($board[$pos_Y][$i])) {
                $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
            } else {
                if ($board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                }
                break;
            }
        }
        for ($j = $pos_Y - 1; $j > 0; $j--) {
            if (is_int($board[$j][$pos_X])) {
                $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
            } else {
                if ($board[$j][$pos_X]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                }
                break;
            }
        }
        for ($j = $pos_Y + 1; $j < 9; $j++) {
            if (is_int($board[$j][$pos_X])) {
                $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
            } else {
                if ($board[$j][$pos_X]->getColor() != $figure->getColor()){
                    $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                }
                break;
            }
        }
        for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++) {  //Так, как и для ладьи. Ищем ходы до тех пор, пока клетка или свободна, или в ней враг другого цвета.
            if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                if (is_int($board[$pos_Y + $i][$pos_X + $i])) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++) {
            if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                if (is_int($board[$pos_Y + $i][$pos_X - $i])) {
                    $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++) {
            if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                if (is_int($board[$pos_Y - $i][$pos_X + $i])) {
                    $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
        for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++) {
            if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                if (is_int($board[$pos_Y - $i][$pos_X - $i])) {
                    $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                } else {
                    if ($board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                    }
                    break;
                }
            }
        }
    }
    return $possibleMoves;
}

function move(\Classes\Figure $figure, array $possibleMoves, array $destination)
{  //Собственно, функция движения
    global $board;
    global $turnNumber;
    global $moveLog;
    if (in_array($destination, $possibleMoves) == False) {
        return ERROR;
    } else {
        if ($destination[2] == 'm') {
            $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
            $board[$destination[1]][$destination[0]] = $figure;
            $figure->setMoveCounter(1);
            $board[$figure->getPositionY()][$figure->getPositionX()] = 0;
            $figure->setPositionY($destination[1]);
            $figure->setPositionX($destination[0]);
            $turnNumber += 1;
            $figure->setType($destination[3]);
            if ($turnNumber % 2 == 1) {  //Для белых
                if (checkForKing('w') == False) {
                    return BLACKWIN;
                }
            } else {
                if (checkForKing('b') == False) {
                    return WHITEWIN;
                }
            }
            return NO_WARNINGS;
        } else if ($destination[2] == 'r') {
            if ($figure->getPositionX() - $destination[0] > 0) {
                $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
                $moveLog[] = [[1, $figure->getPositionY()], [$figure->getPositionX() - 1, $figure->getPositionY()], $figure->getColor()];
                $figure->setPositionX($destination[0]);
                $board[$figure->getPositionY()][1]->setPositionX($destination[0] + 1);
            } else {
                $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
                $moveLog[] = [[8, $figure->getPositionY()], [$figure->getPositionX() + 1, $figure->getPositionY()], $figure->getColor()];
                $figure->setPositionX($destination[0]);
                $board[$figure->getPositionY()][8]->setPositionX($destination[0] - 1);
            }
            $turnNumber += 1;
            return NO_WARNINGS;
        }
    }
    return NO_WARNINGS;
}

function resetGame()
{
    global $moveLog;
    $moveLog = array();
    start_new_game();
    return NO_WARNINGS;
}

