<?php
namespace Classes;


class Figure
{
    public $position_x;  //Её координаты на поле. Вместо букв в позициях X будем использовать числа 1 -- 8.
    public $position_y;
    public $type;  //Тип фигуры
    public $color;  //Цвет фигуры
    public $move_counter = 0; //Счетчик ходов для пешки
    public function __construct($position_x, $position_y)
    {
        $this->position_x = $position_x;  //Задаем стартовые позиции
        $this->position_y = $position_y;
        if ($position_y == 2 || $position_y == 7){ //Назначение ролей в зависимости от координа
            $this->type = 'p';  //Пешка
        } elseif ($position_x == 1 || $position_x == 8){
            $this->type = 'r';  //Ладья
        } elseif ($position_x == 2 || $position_x == 7){
            $this->type = 'n';  //Конь
        } elseif ($position_x == 3 || $position_x == 6){
            $this->type = 'b';  //Слон
        } elseif ($position_x == 4){
            $this->type = 'Q';  //Ферзь
        } else {
            $this->type = 'K';  //Король
        }
        if ($position_x >= 7){  //Назначение цветов
            $this->color = 'b';
        } else {
            $this->color = 'w';
        }
        echo "Created!";
    }

    public function getColor()  //Получение цвета
    {
        return $this->color;
    }

    public function getType()  //Получение типа фигуры
    {
        return $this->type;
    }

    public function getPositionX()  //Получение координаты Y
    {
        return $this->position_x;
    }

    public function getPositionY()  //Получение координаты Y
    {
        return $this->position_y;
    }

    public function setMoveCounter($move_counter)
    {
        $this->move_counter = $move_counter;
    }

    public function getMoveCounter(): int
    {
        return $this->move_counter;
    }
//    public function getPossible()  //Получение возможных ходов для данной фигуры
//    {
//        $possible_moves = array();
//        if ($this->type == 'p'){
//            $possible_moves[] = array(0 => $this->position_x, 1 => $this->position_y + 1);
//            if ($this->move_counter == 0) {
//                $possible_moves[] = array(0 => $this->position_x, 1 => $this->position_y + 2);
//            }
//        } elseif ($this->type == '')
//        return $possible_moves;
//    } Будет в главном файле
}