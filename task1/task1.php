<?php

function permutation($atoms, $index, $size)
{
    $result = [];

    for ($i = 0; $i < $size; $i++) {
        $item = $index % count($atoms);
        $index = floor($index / count($atoms));
        $result[] = $atoms[$item];
        array_splice($atoms, $item, 1);
    }

    return $result;
}

function render_strngs(array $words, $count)
{
    $phrases = [];
    $strings = [];

    // Так как массив из 5 слов может иметь максимум 120 уникальных перемещений
    // я генерирую их сразу и записываю в массив.
    // Этот вариант более быстрый, так как нам нужно просто вытянуть случайное
    // значение из массива, а не генерировать каждый раз новое значение.
    for ($i = 0; $i < 120; $i++) {
        $phrases[] = implode(" ", permutation($words, $i, 5));
    }

    $count_phrases = count($phrases) - 1;

    for ($i = 0; $i < $count; $i++) {
        // Получаем случайный индекс по которому будем брать фразу из згенерированных фраз
        $random_phrases = rand(0, $count_phrases);

        // В этой точке я генерирую массив из 10,000,000 миллионов фраз
        // Но геннерирую двухмерный массив!
        // В индекс я записываю фразу, в значение записываю тоже фразу, хотя она там не нужна
        // но по заданию нужно сгенерировать массив из фраз.
        $strings[$random_phrases][] = $phrases[$random_phrases];
    }

    return $strings;
}

function get_uniques(array $strings)
{
    $uniques = [];

    // Так как общее количество фраз небольшое и все фразы находятся в индексах
    // Нужно перебрать все индексы и найти тот массив который имеет (уникальное) одно значение.
    // Этот способ очень быстрый, так как мы не перебираем все 10,000,000 значений, а перебираем
    // только индексы.
    foreach ($strings as $data) {
        if (count($data) == 1) {
            $uniques[] = $data[0];
        }
    }

    return $uniques;
}

$words = ['red', 'green', 'yellow', 'blue', 'orange'];

$t = microtime(true);
$strings = render_strngs($words, 10000000);
echo "T = ".(microtime(true) - $t)."\n";

$t = microtime(true);
$uniques = get_uniques($strings);
echo "T = ".(microtime(true) - $t)."\n";
print_r($uniques);
