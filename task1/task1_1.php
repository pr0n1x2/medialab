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

        // В отличии от первого способа здесь я генерирую одномерный массих из случайных фраз.
        // Так как в задании не было указано, что можно генерировать двухмерный массив (что я сделал в первом варианте)
        // Тут я генерирую массив из 10,000,000 фраз
        $strings[] = $phrases[$random_phrases];
    }

    return $strings;
}

function get_uniques(array $strings)
{
    $uniques = [];
    $keys = [];

    // Так как мы имеем небольшой диапазон уникальных фраз, мы можем сохранять
    // их под индексами, а в значение записывать их количество.
    // В этом случае нам не нужна операция сравнения фраз.
    //
    // Этот способ значительно затратнее по времени в отличие от первого способа
    // так как нам нужно перебрать все 10,000,000 значений в массиве.
    for ($i = 0; $i < count($strings); $i++) {
        $keys[$strings[$i]]++;
    }

    // Для того, чтобы найти униканые значения, нужно перебрать все индексы
    // и найти где количество равно 1
    foreach ($keys as $key => $value) {
        if ($value == 1) {
            $uniques = $key;
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
