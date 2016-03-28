<?php

return \yay_compile(__FILE__); __halt_compiler();

declare(strict_types=1);

namespace Yay\Functions;

function partial($fn)
{
  // Fetch the initial parameters on initialization
  $start_parameters = array_slice(func_get_args(), 1);
  $required_size = (new \ReflectionFunction($fn))->getNumberOfRequiredParameters();
  // When we have enough arguments to evaluate the function, the edge-case.
  if (sizeof($start_parameters) >= $required_size) {
    return call_user_func_array($fn, $start_parameters);
  }
  // When we must partialize it
  return function() use ($start_parameters, $required_size, $fn) {
    $rest_parameters = func_get_args();
    $remaining_size = $required_size - (count($rest_parameters) + count($start_parameters));
    // Join the current parameters with the newly received parameters
    $all_params = array_merge($start_parameters, $rest_parameters);
    // Append the function as the first item and call partialization again
    array_unshift($all_params, $fn);
    return call_user_func_array('partial', $all_params);
  };
}

# TODO: Assert syntax
# TODO: Use ellipsis for multiple parameters for partial, but without
#       parenthesis.
#       GRAMMAR := partial expr { , expr }
macro ·global ·unsafe {
  partial ·expr
} >> {
  partial(·expr)
}

/**
 * TODO:
 * - Write README.
 * - Use Composer.
 * - Write tests
 *
 * EXAMPLES:
 * $add = partial function(double $a, double $b): double {
 *   return $a + $b;
 * };
 *
 * $add_10 = $add(10); // function($b) { return 10 + $b; }
 * $add_10(20);        // 30;
 *
 * $double_list = partial 'array_map', function($x) { return $x * 2; }
 * $double_list(range(1, 5)); // [2, 4, 6, 8, 10]
 */
