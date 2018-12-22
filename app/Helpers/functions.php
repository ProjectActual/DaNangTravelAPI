<?php

function save_image($image, $link)
{
    $file = $image;
    $extension = $file->getClientOriginalExtension();
    $name = Uuid::generate(4) . '.' . $extension;
    $file->move($link, $name);

    return '/' . $link . $name;
}
