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
        if ($position_y == 2 || $position_y == 7){ //Назначение ролей в зависимости от координаты
            $this->type = 'p';  //Пешка
        } elseif ($position_x == 1 || $position_x == 8){
            $this->type = 'r';  //Ладья
        } elseif ($position_x == 2 || $position_x == 7){
            $this->type = 'n';  //Конь
        } elseif ($position_x == 3 || $position_x == 6){
            $this->type = 'b';  //Слон
        } elseif ($position_x == 4){
            $this->type = 'q';  //Ферзь
        } else {
            $this->type = 'k';  //Король
        }
        if ($position_y >= 7){  //Назначение цветов
            $this->color = 'b';
        } else if ($position_y <= 2){
            $this->color = 'w';
        }
//        echo "Created!";  Отладочное
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

    public function getMoveCounter()
    {
        return $this->move_counter;
    }

    public function setType(string $type)  //Установка параметра фигуры (для смены типа пешки)
    {
        $this->type = $type;
    }

    public function setPositionX($position_x)
    {
        $this->position_x = $position_x;
    }

    public function setPositionY($position_y)
    {
        $this->position_y = $position_y;
    }

}