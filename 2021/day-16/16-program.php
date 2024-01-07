<?php
/**
 * Day 16: Packet Decoder
 *
 * @link https://adventofcode.com/2021/day/16
 */

/**
 * Run the program as:
 *   php 16-program.php
 * or
 *   php 16-program.php D2FE28
 *   php 16-program.php 38006F45291200
 *   php 16-program.php EE00D40C823060
 *   php 16-program.php 8A004A801A8002F478
 *   php 16-program.php 620080001611562C8802118E34
 *   php 16-program.php C0015000016115A2E0802F182340
 *   php 16-program.php A0016C880162017C3686B18A3D4780
 *   php 16-program.php C200B40A82
 *   php 16-program.php 04005AC33890
 *   php 16-program.php 880086C3E88112
 *   php 16-program.php CE00C43D881120
 *   php 16-program.php D8005AC2A8F0
 *   php 16-program.php F600BC2D8F
 *   php 16-program.php 9C005AC2F8F0
 *   php 16-program.php 9C0141080250320F1802104A08
 */

// Read the input data from file or from the command line (the examples)
if ($argc >= 2) {
  $inputdata = $argv[1];
} else {
  $inputdata = trim(file_get_contents(dirname($argv[0]).'/16-input'));
}

$transmission = new Transmission($inputdata);
$packet = $transmission->parseNextPacket();

// Part 1
printf("part 1: %d\n", $packet->sumVersionNumbers());

// Part 2
printf("part 2: %d\n", $packet->getValue());



class Transmission {
  // Input data
  private string $data;
  private string $rest;

  public function __construct(string $input) {
    // Convert from hex to bin, one hex char at a time
    $data = [];
    foreach (str_split($input) as $nibble) {
      $data[] = sprintf('%04b', hexdec($nibble));
    }
    $this->data = implode('', $data);

    // The transmission bits remaining to parse
    $this->rest = $this->data;
  }

  public function parseNextPacket(): Packet {
    // Parse the packet header
    $version = $this->getNextBits(3);
    $typeId = $this->getNextBits(3);

    // Create the packet
    switch ($typeId) {
      case 0: $packet = new SumOperator($version); break;
      case 1: $packet = new ProductOperator($version); break;
      case 2: $packet = new MinimumOperator($version); break;
      case 3: $packet = new MaximumOperator($version); break;
      case 4: $packet = new LiteralValue($version); break;
      case 5: $packet = new GreaterThanOperator($version); break;
      case 6: $packet = new LessThanOperator($version); break;
      case 7: $packet = new EqualOperator($version); break;
      default: $packet = new Operator($version, $typeId); break;
    }

    // Let the packet parse its data
    $packet->parse($this);

    return $packet;
  }

  public function getNextBits(int $nbBits): int {
    $number = bindec(substr($this->rest, 0, $nbBits));
    $this->rest = substr($this->rest, $nbBits);
    return $number;
  }
}

abstract class Packet {
  // The length in bits
  protected int $length;

  public function __construct(protected int $version) {
    $this->length = 3 + 3;
  }

  abstract public function parse(Transmission $data): void;

  public function getLength(): int {
    return $this->length;
  }

  abstract public function getValue(): int;

  public function sumVersionNumbers(): int {
    return $this->version;
  }

  protected function getNextBits(int $nbBits, Transmission $data): int {
    $this->length += $nbBits;
    return $data->getNextBits($nbBits);
  }
}

class LiteralValue extends Packet {
  protected int $value;

  public function parse(Transmission $data): void {
    $value = 0;
    $last = false;
    do {
      $group = $this->getNextBits(5, $data);

      $value = $value * 16 + $group % 16;
      $last = $group < 16;
    } while (! $last);

    $this->value = $value;
  }

  public function getValue(): int { return $this->value; }
}

abstract class Operator extends Packet {
  protected array $subpackets;

  public function parse(Transmission $data): void {
    $this->subpackets = [];

    // The lengthTypeId describes the subpackets are counted (by bits or by number)
    $lengthTypeId = $this->getNextBits(1, $data);

    if ($lengthTypeId === 0) {
      // The next 15 bits represent the total length of the subpackets
      $totalLength = $this->getNextBits(15, $data);

      $this->readSubpacketsByTotalLength($totalLength, $data);
    } else {
      // The next 11 bits tells the number of subpacket
      $nbSubpackets = $this->getNextBits(11, $data);

      $this->readSubpacketsByNumber($nbSubpackets, $data);
    }
  }

  private function readSubpacketsByTotalLength(int $totalLength, Transmission $data): void {
    // Read the subpackets
    $length = 0;

    do {
      $packet = $data->parseNextPacket();
      $this->subpackets[] = $packet;
      $length += $packet->getLength();
    } while ($length < $totalLength);

    // Update the size of this packet
    $this->length += $totalLength;
  }

  private function readSubpacketsByNumber(int $nbSubpackets, Transmission $data): void {
    // Read the subpackets
    for ($i = 0; $i < $nbSubpackets; $i ++) {
      $packet = $data->parseNextPacket();
      $this->subpackets[] = $packet;

      // Update the size of this packet
      $this->length += $packet->getLength();
    }
  }

  public function sumVersionNumbers(): int {
    return $this->version + array_sum(
      array_map(
        fn($packet) => $packet->sumVersionNumbers(),
        $this->subpackets
      )
    );
  }
}

class SumOperator extends Operator {
  public function getValue(): int {
    return array_sum(array_map(fn($p) => $p->getValue(), $this->subpackets));
  }
}

class ProductOperator extends Operator {
  public function getValue(): int {
    return array_product(array_map(fn($p) => $p->getValue(), $this->subpackets));
  }
}

class MinimumOperator extends Operator {
  public function getValue(): int {
    return min(array_map(fn($p) => $p->getValue(), $this->subpackets));
  }
}

class MaximumOperator extends Operator {
  public function getValue(): int {
    return max(array_map(fn($p) => $p->getValue(), $this->subpackets));
  }
}

class GreaterThanOperator extends Operator {
  public function getValue(): int {
    return $this->subpackets[0]->getValue() > $this->subpackets[1]->getValue() ? 1 : 0;
  }
}

class LessThanOperator extends Operator {
  public function getValue(): int {
    return $this->subpackets[0]->getValue() < $this->subpackets[1]->getValue() ? 1 : 0;
  }
}

class EqualOperator extends Operator {
  public function getValue(): int {
    return $this->subpackets[0]->getValue() === $this->subpackets[1]->getValue() ? 1 : 0;
  }
}


// That's all, folks!
