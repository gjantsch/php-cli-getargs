<?php
/**
 * Test CLIGetArgs class.
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 */

require 'CLIGetArgs.php';

$args = CLIGetArgs::getInstance();

echo "Running " . $args->getScript() . " with " . $args->count() . " arguments: " . PHP_EOL;
foreach($args->getOptions() as $arg => $value) {
    echo $arg . ' = ' . $args->get($arg) . PHP_EOL;
}

echo "Total operands: " . $args->getOperandCounter() . PHP_EOL;

while($operand = $args->popOperand()) {
    echo "Operand " . $operand . PHP_EOL;
}