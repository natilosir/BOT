<?php

// Package bootstrap intentionally stays lightweight. Route dispatch is registered
// lazily by Route::add()/Route::def(), so index.php needs no Container, Facade or init call.
