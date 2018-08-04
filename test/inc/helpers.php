<?php

/**
 * setTimeout implementation similar API to JavaScript's setTimeout
 * highly coupled to Tix, probably not the nicest code
 *
 * @param callable $fn
 * @param int $timeout
 *
 * @return \Closure
 */
function setTimeout(callable $fn, int $timeout=0) : \Closure {
  $start = time();
  return function() use ($start, $timeout, &$fn) {
      $func = function() use ($start, $timeout, &$func, &$fn) {
          $tick = time();
          $diff = ($tick - $start);
          if ( $diff >= $timeout ) {
              $fn->bindTo($this)();
          } else {
              if($this->isActive()) {
                  $this->push($func);
              }
          }
      };
      if($this->isActive()) {
          $this->push($func);
      }
  };
}

/**
 * Same as setTimeout but calls itself after the timeout is reached
 * Attempted to do this passing references. Didn't work
 *
 * @param callable $fn
 * @param int $timeout
 *
 * @return \Closure
 */
function setInterval(callable $fn, int $timeout=0) : \Closure {
  $start = time();
  return function() use ($start, $timeout, &$fn) {
      $func = function() use ($start, $timeout, &$func, &$fn) {
          $tick = time();
          $diff = ($tick - $start);
          if ( $diff >= $timeout ) {
              $fn->bindTo($this)();
              setInterval($fn, $timeout);
          } else {
              if($this->isActive()) {
                  $this->push($func);
              }
          }
      };
      if($this->isActive()) {
          $this->push($func);
      }
  };
}
