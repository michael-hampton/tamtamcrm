<?php
class DateParser {
    private $variables = [];

    public function evaluate($string)
    {
       $this->variables = ['MONTH' => 'F', 'YEAR' => 'Y', 'QUARTER' => 'QUARTER', 'MONTHYEAR' => 'F Y'];

        $stack = $this->parse($string);
        
        return $this->run($stack);
    }

    private function parse($string)
    {
        $tokens = $this->tokenize($string);
        
        $output = new Stack();
        $operators = new Stack();
        foreach ($tokens as $token) {
            $token = $this->extractVariables($token);
            
            //$expression = TerminalExpression::factory($token);
            if (in_array($token, ['+', '-', '/', '*'])) {
            
                $this->parseOperator($token, $output, $operators);
            } elseif ($this->isParenthesis($token)) {
                $this->parseParenthesis($token, $output, $operators);
            } else {
                $output->push($token);
            }
        }
        while (($op = $operators->pop())) {
            /* if ($op->isParenthesis()) {
                throw new \RuntimeException('Mismatched Parenthesis');
            } */
            $output->push($op);
        }

        return $output;
    }

    private function isParenthesis($expression) {
        return $expression === 'to';
    }

    private function registerVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    private function run(Stack $stack)
    {
        
        while (($operator = $stack->pop()) && in_array($operator, ['+', '-', '/', '*'])) {
        
        
            $numerator = $stack->pop();
            $type = $stack->pop();
        
            $date = date('Y-m-d');
            $date = new DateTime($date);
        
            switch($type) {
                case 'F':
                case 'F Y':
                    $date = $date->modify($operator . $numerator . 'months');
                break;
            
                case 'Y':
                    $date = $date->modify($operator . $numerator . 'years');
                break;
            }
        
            $format = $type === 'F Y' ? 'F Y' : 'Y-F-d';
        
            $value = $type === 'QUARTER' ? $this->calculateQuarters($numerator) : $date->format($format);
       
            if (!empty($value)) {
                $stack->push($value);
            }
            
           
           break;
           
        }
        
        return $this->render($stack);
    }   

    private function calculateQuarters($numerator) {
        $current = floor((date('n') - 1) / 3);

        $year = date('y');
        $quarters = array();

        for ($i = 0; $i < 12; $i++) {
            $q = (($current+$i)%4) + 1;
            $quarters[] = "Q" . $q . "-" . $year;

            if (($current+$i+1)%4 == 0) {
                $year++;
            }
        }
           
        return $quarters[$numerator];

    }

    private function extractVariables($token)
    {
        if ($token[0] == '$') {
            $key = substr($token, 1);

            return isset($this->variables[$key]) ? $this->variables[$key] : 0;
        }

        return $token;
    }
      
    private function render(Stack $stack)
    {
        $output = '';
        while (($el = $stack->shift())) {
           
           $output .= ' ' . $el;
           
        }
        if ($output) {
            return $output;
        }
        throw new \RuntimeException('Could not render output');
    }

    private function parseParenthesis($expression, Stack $output, Stack $operators)
    {
        $type = $output->pop();
    
        if($type === 'QUARTER') {
            $month = date("n");

            //Calculate the year quarter.
            $yearQuarter = ceil($month / 3);

            //Print it out
            $output->push("Q$yearQuarter-" . date('y'));
        } else {
            $output->push(date($type));
        }
    
        $output->push($expression);
    
    }

    private function parseOperator($expression, Stack $output, Stack $operators)
    {
    
        $end = $operators->poke();
       
        if (!$end) {
            $operators->push($expression);
        } elseif (in_array($end, ['+', '-', '/', '*'])) {
            do {
                
                    $output->push($operators->pop());
                
            } while (($end = $operators->poke()) && in_array($end, ['+', '-', '/', '*']));
            $operators->push($expression);
        } else {
            $operators->push($expression);
        }
      
    }

    private function tokenize($string)
    {
        $parts = preg_split('((\d+\.?\d+|\+|-|\(|\)|\*|/)|\s+)', $string, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $parts = array_map('trim', $parts);
        foreach ($parts as $key => &$value) {
            //if this is the first token or we've already had an operator or open paren, this is unary
            if ($value == '-') {
                if ($key - 1 < 0 || in_array($parts[$key - 1], array('+', '-', '*', '/', '('))) {
                    $value = 'u';
                }
            }
        }

        return $parts;
    }
}

class Stack
{
    protected $data = array();

    public function push($element)
    {
        $this->data[] = $element;
    }

    public function poke()
    {
        return end($this->data);
    }

    public function pop()
    {
        return array_pop($this->data);
    }
    
    public function shift()
    {
        return array_shift($this->data);
    }

    //check out the end of the array without changing the pointer via http://stackoverflow.com/a/7490837/706578
    public function peek()
    {
        return current(array_slice($this->data, -1));
    }
    
    public function count()
    {
        return count($this->data);
    }
}
