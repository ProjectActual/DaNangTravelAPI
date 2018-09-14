<?php

function responses($message, $status, $data = [], $error = false)
{
    if($status > 102  && $status <= 202) {
        return response()->json(compact('error', 'message', 'status', 'data'), $status);
    } else {
        $error = true;
        return response()->json(compact('error', 'message', 'status'), $status);
    }
}
