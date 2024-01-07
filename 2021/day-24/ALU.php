<?php



class ALU {
  private int $x;
  private int $y;
  private int $z;
  private int $w;

  private int $ip;

  public function __construct(private array $program, private array $input) {
    $this->x = 0;
    $this->y = 0;
    $this->z = 0;
    $this->w = 0;

    $this->ip = 0;
  }

  public function run() {
    while ($this->ip < count($this->program)) {
      $statement = $this->createStatement($this->program[$this->ip]);
      $statement->execute($this);

      $this->ip ++;
    }
  }

  public function isValidInput(): bool {
    return $this->z === 0;
  }

  public function read(string $dest) {
    $this->getRegisterValue($dest);        // for validation
    if (count($this->input) === 0) {
      throw new Exception("A read statement has been reached but there is no data in the input buffer");
    }
    $this->{$dest} = intval(array_shift($this->input));
  }

  public function add(string $op1, string $op2) {
    $this->{$op1} = $this->getRegisterValue($op1) + $this->getRegisterOrImmediate($op2);
  }

  public function multiply(string $op1, string $op2) {
    $this->{$op1} = $this->getRegisterValue($op1) * $this->getRegisterOrImmediate($op2);
  }

  public function divide(string $op1, string $op2) {
    $divider = $this->getRegisterOrImmediate($op2);
    if ($divider === 0) {
      throw new Exception("Division by zero");
    }
    $this->{$op1} = intval($this->getRegisterValue($op1) / $divider);
  }

  public function modulo(string $op1, string $op2) {
    $a = $this->getRegisterValue($op1);
    if ($a < 0) {
      throw new Exception("Invalid 'div' operation. $a < 0");
    }
    $b = $this->getRegisterOrImmediate($op2);
    if ($b <= 0) {
      throw new Exception("Invalid 'div' operation. $b <= 0");
    }
    $this->{$op1} = $a % $b;
  }

  public function equals(string $op1, string $op2) {
    $a = $this->getRegisterValue($op1);
    $b = $this->getRegisterOrImmediate($op2);
    $this->{$op1} = $a === $b ? 1 : 0;
  }

  public function __toString(): string {
    return "x={$this->x}; y={$this->y}; z={$this->z}; w={$this->w}; ip={$this->ip} (remaining: ".(count($this->program) - $this->ip)."); input-size=".count($this->input);
  }

  private function createStatement(array $words): Statement {
    switch ($words[0]) {
      case 'inp': return new Input($words[1]);
      case 'add': return new Add($words[1], $words[2]);
      case 'mul': return new Multiply($words[1], $words[2]);
      case 'div': return new Divide($words[1], $words[2]);
      case 'mod': return new Modulo($words[1], $words[2]);
      case 'eql': return new Equals($words[1], $words[2]);
      default: throw new Exception("Unknown statement '{$words[0]}'");
    }
  }

  private function getRegisterValue(string $register): int {
    if (! in_array($register, ['x', 'y', 'z', 'w'])) {
      throw new Exception("Invalid register '$register'");
    }

    return $this->{$register};
  }

  private function getRegisterOrImmediate(string $operand): int {
    if (in_array($operand, ['x', 'y', 'z', 'w'])) {
      return $this->{$operand};
    }

    if (! is_numeric($operand)) {
      throw new Exception("The value '$operand' is not a register name and is also not a number.");
    }
    return intval($operand);
  }
}

abstract class Statement {
  protected string $first;

  abstract public function execute(ALU $computer): void;
}

class Input extends Statement {
  public function __construct(protected string $first) {}

  public function execute(ALU $computer): void {
    $computer->read($this->first);
  }
}

class Add extends Statement {
  public function __construct(protected string $first, protected string $second) {}

  public function execute(ALU $computer): void {
    $computer->add($this->first, $this->second);
  }
}

class Multiply extends Statement {
  public function __construct(protected string $first, protected string $second) {}

  public function execute(ALU $computer): void {
    $computer->multiply($this->first, $this->second);
  }
}

class Divide extends Statement {
  public function __construct(protected string $first, protected string $second) {}

  public function execute(ALU $computer): void {
    $computer->divide($this->first, $this->second);
  }
}

class Modulo extends Statement {
  public function __construct(protected string $first, protected string $second) {}

  public function execute(ALU $computer): void {
    $computer->modulo($this->first, $this->second);
  }
}

class Equals extends Statement {
  public function __construct(protected string $first, protected string $second) {}

  public function execute(ALU $computer): void {
    $computer->equals($this->first, $this->second);
  }
}


// That's all, folks!
