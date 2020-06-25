<?php

namespace Classes;


class Game
{

    private $turnNumber;
    private $roqueBlack;
    private $roqueWhite;
    private $moveLog;
    function __construct()
    {
        $this->board = Null;
        $this->board = array_fill(1, 8, array_fill(1, 8, 0));  //Массив, содержащий сведения о доске
        //Если в клетке нет фигуры, то в ней 0, иначе -- фигура со всеми ее параметрами
        for ($j = 1; $j <= 2; $j++) {
            for ($i = 1; $i <= 8; $i++) {
                $figure = new Figure($i, $j);  //Создание фигуры по ее координатам
                $this->board[$j][$i] = $figure; //Запись в массив доски фигуры
                $figure = NULL;
            }
        }
        for ($j = 7; $j <= 8; $j++) { //Та же история, только с другим цветом
            for ($i = 1; $i <= 8; $i++) {
                $figure = new Figure($i, $j);
                $this->board[$j][$i] = $figure;
                $figure = NULL;
            }
        }
//    echo "Game\'s ready for you! First turn is for white"; Необязательно
        $this->turnNumber = 1;
        $this->roqueBlack = 0;
        $this->roqueWhite = 0;
        $this->moveLog = array();
        return $this->board;
    }

    function getPossibleFigures()
    {  //Возвращает возможные фигуры для конкретного игрока
        $possibleFigures = array(); //Массив доступных фигур
        if ($this->turnNumber % 2 == 1) {  //Для белых
            foreach ($this->board as $row) {
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
            foreach ($this->board as $row) {
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
    }  //Боюсь, что функция совершенно не нужна

    private function checkForKing(string $color)
    {  //Функция проверяет на наличие короля нужного цвета на доске
        foreach ($this->board as $row) {
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

    public function getPossibleMoves(Figure $figure)
    {  //Определение возможных ходов для заданной фигуры
        /** Формат элемента массива возможных ходов
         *  Сначала две координаты назначения, затем тип хода ('m' -- простой, 'r' -- рокировка), затем -- код фигуры, в которую превратится данная фигура после хода (для смены типа пешкой)
         **/
        $possibleMoves = array();
        $pos_X = $figure->getPositionX(); //Заполнение координат X и Y
        $pos_Y = $figure->getPositionY();
        if ($figure->getType() == 'p') {  //Логика ходьбы для пешки
            if ($figure->getColor() == 'w') {
                if ($pos_X != 8 && is_object($this->board[$pos_Y + 1][$pos_X + 1])) {
                    if ($this->board[$pos_Y + 1][$pos_X + 1]->getColor() != $figure->getColor()) {
                        if ($pos_Y + 1 == 8) {
                            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'r'];
                            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'q'];
                            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'n'];
                            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'b'];  //Механизм смены типа пешки при достижении края доски
                        } else {
                            $possibleMoves[] = [$pos_X + 1, $pos_Y + 1, 'm', 'p'];  //Обычный ход
                        }
                    }
                } else if ($pos_X != 1 && is_object($this->board[$pos_Y + 1][$pos_X - 1])){
                    if ($this->board[$pos_Y + 1][$pos_X - 1]->getColor() != $figure->getColor()) {
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
                } else if ($pos_Y + 1 < 8 && is_int($this->board[$pos_Y + 1][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $pos_Y + 1, 'm', 'p']; //Движение вперед обычное
                    if ($figure->getMoveCounter() == 0 && is_int($this->board[$pos_Y + 2][$pos_X])) {
                        $possibleMoves[] = [$pos_X, $pos_Y + 2, 'm', 'p']; //Первый ход пешки
                    }
                }
            } else if ($figure->getColor() == 'b') {
                if ($pos_X != 1 && is_object($this->board[$pos_Y - 1][$pos_X - 1])){
                    if ($this->board[$pos_Y - 1][$pos_X - 1]->getColor() != $figure->getColor()) {
                        if ($pos_Y - 1 == 1) {
                            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'r'];
                            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'q'];
                            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'n'];
                            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'b'];
                        } else {
                            $possibleMoves[] = [$pos_X - 1, $pos_Y - 1, 'm', 'p'];
                        }
                    }
                } else if ($pos_X != 8 && is_object($this->board[$pos_Y - 1][$pos_X + 1])){
                    if ($this->board[$pos_Y - 1][$pos_X + 1]->getColor() != $figure->getColor()) {
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
                } else if ($pos_Y - 1 > 1 && is_int($this->board[$pos_Y - 1][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $pos_Y - 1, 'm', 'p'];
                    if ($figure->getMoveCounter() == 0 && is_int($this->board[$pos_Y - 2][$pos_X])) {
                        $possibleMoves[] = [$pos_X, $pos_Y - 2, 'm', 'p'];
                    }
                }
            }
        } else if ($figure->getType() == 'r') {   //Логика ходов для ладьи
            for ($i = $pos_X - 1; $i > 0; $i--) {
                if (is_int($this->board[$pos_Y][$i])) { //Ищем ходы в каждую сторону, пока не натыкаемся на врага или своего. Если враг, то его клетку записываем, иначе -- нет
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                } else {
                    if ($this->board[$pos_Y][$i]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                    }
                    break;
                }
            }
            for ($i = $pos_X + 1; $i < 9; $i++) {
                if (is_int($this->board[$pos_Y][$i])) {
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                } else {
                    if ($this->board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$i, $pos_Y, 'm', 'r'];
                    }
                    break;
                }
            }
            for ($j = $pos_Y - 1; $j > 0; $j--) {
                if (is_int($this->board[$j][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                } else {
                    if ($this->board[$j][$pos_X]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                    }
                    break;
                }
            }
            for ($j = $pos_Y + 1; $j < 9; $j++) {
                if (is_int($this->board[$j][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                } else {
                    if ($this->board[$j][$pos_X]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$pos_X, $j, 'm', 'r'];
                    }
                    break;
                }
            }
        } else if ($figure->getType() == 'b') {  //Логика ходов для слона
            for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++) {  //Так, как и для ладьи. Ищем ходы до тех пор, пока клетка или свободна, или в ней враг другого цвета.
                if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                    if (is_int($this->board[$pos_Y + $i][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++) {
                if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                    if (is_int($this->board[$pos_Y + $i][$pos_X - $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++) {
                if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                    if (is_int($this->board[$pos_Y - $i][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++) {
                if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                    if (is_int($this->board[$pos_Y - $i][$pos_X - $i])) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
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
                        if (is_int($this->board[$pos_Y + $j][$pos_X + $i])) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                        } else {
                            if ($this->board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                                $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                            }
                        }
                    }
                }
            }
            foreach ($possible_changes_2 as $i) {  //Просто перебор всех комбинаций вида (+-2, +-1)
                foreach ($possible_changes_1 as $j) {
                    if (1 <= $pos_X + $i && $pos_X + $i <= 8 && 1 <= $pos_Y + $j && $pos_Y + $j <= 8) {
                        if (is_int($this->board[$pos_Y + $j][$pos_X + $i])) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'n'];
                        } else {
                            if ($this->board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
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
                            if (is_int($this->board[$pos_Y + $j][$pos_X + $i])) {
                                $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'k'];
                            } else {
                                if ($this->board[$pos_Y + $j][$pos_X + $i]->getColor() != $figure->getColor()) {
                                    $possibleMoves[] = [$pos_X + $i, $pos_Y + $j, 'm', 'k'];
                                }
                            }
                        }
                    }
                }
            }
            if ($pos_Y == 1) {
                if ($figure->getMoveCounter() == 0 && $this->roqueWhite == 0) {
                    if (is_object($this->board[$pos_Y][1])) {
                        if ($this->board[$pos_Y][1]->getMoveCounter() != 0) {
                            $broken = False;
                            for ($i = 2; $i < $pos_X; $i++) {  //Проверяем, пустые ли клетки
                                if (is_int($this->board[$pos_Y][$i])) {
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
                    if (is_object($this->board[$pos_Y][8])) {  //Проверяем, пустые ли клетки в другую сторону
                        if ($this->board[$pos_Y][8]->getMoveCounter() != 0) {
                            $broken = False;
                            for ($i = 8; $i > $pos_X; $i--) {
                                if (is_int($this->board[$pos_Y][$i])) {
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
                if ($figure->getMoveCounter() == 0 && $this->roqueBlack == 0) {
                    if (is_object($this->board[$pos_Y][1])) {
                        if ($this->board[$pos_Y][1]->getMoveCounter() != 0) {
                            $broken = False;
                            for ($i = 2; $i < $pos_X; $i++) {  //Проверяем, пустые ли клетки
                                if (is_int($this->board[$pos_Y][$i])) {
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
                    if (is_object($this->board[$pos_Y][8])) {  //Проверяем, пустые ли клетки в другую сторону
                        if ($this->board[$pos_Y][8]->getMoveCounter() != 0) {
                            $broken = False;
                            for ($i = 8; $i > $pos_X; $i--) {
                                if (is_int($this->board[$pos_Y][$i])) {
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
                if (is_int($this->board[$pos_Y][$i])) { //Ищем ходы в каждую сторону, пока не натыкаемся на врага или своего. Если враг, то его клетку записываем, иначе -- нет
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                } else {
                    if ($this->board[$pos_Y][$i]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                    }
                    break;
                }
            }
            for ($i = $pos_X + 1; $i < 9; $i++) {
                if (is_int($this->board[$pos_Y][$i])) {
                    $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                } else {
                    if ($this->board[$pos_Y][$i]->getColor() != $figure->getColor()) {
                        $possibleMoves[] = [$i, $pos_Y, 'm', 'q'];
                    }
                    break;
                }
            }
            for ($j = $pos_Y - 1; $j > 0; $j--) {
                if (is_int($this->board[$j][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                } else {
                    if ($this->board[$j][$pos_X]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                    }
                    break;
                }
            }
            for ($j = $pos_Y + 1; $j < 9; $j++) {
                if (is_int($this->board[$j][$pos_X])) {
                    $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                } else {
                    if ($this->board[$j][$pos_X]->getColor() != $figure->getColor()){
                        $possibleMoves[] = [$pos_X, $j, 'm', 'q'];
                    }
                    break;
                }
            }
            for ($i = 1; $i < min(8 - $pos_X, 8 - $pos_Y); $i++) {  //Так, как и для ладьи. Ищем ходы до тех пор, пока клетка или свободна, или в ней враг другого цвета.
                if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                    if (is_int($this->board[$pos_Y + $i][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y + $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y + $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min(8 - $pos_X, $pos_Y - 1); $i++) {
                if (1 <= $pos_Y + $i && $pos_Y + $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                    if (is_int($this->board[$pos_Y + $i][$pos_X - $i])) {
                        $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y + $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X + $i, $pos_Y - $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min($pos_X - 1, 8 - $pos_Y); $i++) {
                if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X + $i && $pos_X + $i <= 8) {
                    if (is_int($this->board[$pos_Y - $i][$pos_X + $i])) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y - $i][$pos_X + $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X - $i, $pos_Y + $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
            for ($i = 1; $i < min($pos_X - 1, $pos_Y - 1); $i++) {
                if (1 <= $pos_Y - $i && $pos_Y - $i <= 8 && 1 <= $pos_X - $i && $pos_X - $i <= 8) {
                    if (is_int($this->board[$pos_Y - $i][$pos_X - $i])) {
                        $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                    } else {
                        if ($this->board[$pos_Y - $i][$pos_X - $i]->getColor() != $figure->getColor()) {
                            $possibleMoves[] = [$pos_X - $i, $pos_Y - $i, 'm', 'b'];
                        }
                        break;
                    }
                }
            }
        }
        return $possibleMoves;
    }

    function move(Figure $figure, array $possibleMoves, array $destination)
    {  //Собственно, функция движения
        if (in_array($destination, $possibleMoves) == False) {
            return ERROR;
        } else {
            if ($destination[2] == 'm') {
                $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
                $this->board[$destination[1]][$destination[0]] = $figure;
                $figure->setMoveCounter(1);
                $this->board[$figure->getPositionY()][$figure->getPositionX()] = 0;
                $figure->setPositionY($destination[1]);
                $figure->setPositionX($destination[0]);
                $this->turnNumber += 1;
                $figure->setType($destination[3]);
                if ($this->turnNumber % 2 == 1) {  //Для белых
                    if (checkForKing('w') == False) {
                        return BLACKWIN;
                    }
                } else {
                    if (checkForKing('b') == False) {
                        return WHITEWIN;
                    }
                }
            } else if ($destination[2] == 'r') {
                if ($figure->getPositionX() - $destination[0] > 0) {
                    $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
                    $moveLog[] = [[1, $figure->getPositionY()], [$figure->getPositionX() - 1, $figure->getPositionY()], $figure->getColor()];
                    $figure->setPositionX($destination[0]);
                    $this->board[$figure->getPositionY()][1]->setPositionX($destination[0] + 1);
                } else {
                    $moveLog[] = [[$figure->getPositionX(), $figure->getPositionY()], [$destination[0], $destination[1]], $figure->getColor()];
                    $moveLog[] = [[8, $figure->getPositionY()], [$figure->getPositionX() + 1, $figure->getPositionY()], $figure->getColor()];
                    $figure->setPositionX($destination[0]);
                    $this->board[$figure->getPositionY()][8]->setPositionX($destination[0] - 1);
                }
                $this->turnNumber += 1;
            }
        }
        return $this->board;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function getMoveLog()
    {
        return $this->moveLog;
    }
}