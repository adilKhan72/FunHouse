<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/SingleManningCalculator.php';
require_once __DIR__ . '/../src/Rota.php';
require_once __DIR__ . '/../src/Shift.php';

use PHPUnit\Framework\TestCase;

class SingleManningCalculatorTest extends TestCase
{
    protected $calculator;

    protected function setUp(): void
    {
        $this->calculator = new SingleManningCalculator();
    }

    // Scenario One
        // Black Widow: |----------------------|  From 'next monday 09:00:00' to 'next monday 16:00:00'
        // Given Black Widow working at FunHouse on Monday in one long shift
        // When no-one else works during the day
        // Then Black Widow receives single manning supplement for the whole duration of her shift.
        // When calculate shift and remove breaks duration from shift duration we get 420 singleManning Minutes for Monday (2023-05-15)
    public function testScenarioOne()
    {
        $rota = new Rota();

        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::BLACK_WIDOW, new DateTime('next monday 09:00:00'), new DateTime('next monday 16:00:00')));
        
        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 420,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);

        $result = $this->calculator->calculateSingleManning($rota);
        // print_r([$expected, $result]); exit;
        // Calling calculateSingleManning method for calcuting single manning from rota shifts.
        
        // Checking if the result and expected data are equal.
        $this->assertEquals($expected, $result);
    }


    // Scenario Two
        // Black Widow: |----------| From 'next tuesday 09:00:00' to 'next tuesday 13:00:00'
        // Thor:                   |-------------| From 'next tuesday 13:00:00' to 'next tuesday 17:00:00'
        // Given Black Widow and Thor working at FunHouse on Tuesday
        // When they only meet at the door to say hi and bye
        // Then Black Widow receives single manning supplement for the whole duration of her shift
        // And Thor also receives single manning supplement for the whole duration of his shift.
        // When calculate shift and remove breaks duration from shift duration we get 480 singleManning Minutes for Tuesday
    public function testScenarioTwo()
    {
        $rota = new Rota();
        
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::BLACK_WIDOW, new DateTime('next monday + 1 days 09:00:00'), new DateTime('next monday + 1 days 13:00:00')));
        $rota->addShift(new Shift(Shift::THOR, new DateTime('next monday + 1 days 13:00:00'), new DateTime('next monday + 1 days 17:00:00')));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 480,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);


        $result = $this->calculator->calculateSingleManning($rota); 
        $this->assertEquals($expected, $result);
    }

    // Scenario Three
        // Wolverine: |------------| From 'next wednesday 09:00:00' to 'next wednesday 13:00:00'
        // Gamora:       |-----------------| From 'next wednesday 09:00:00' to 'next wednesday 13:00:00'
        // Given Wolverine and Gamora working at FunHouse on Wednesday
        // When Wolverine works in the morning shift
        // And Gamora works the whole day, starting slightly later than Wolverine
        // Then Wolverine receives single manning supplement until Gamorra starts her shift
        // And Gamorra receives single manning supplement starting when Wolverine has finished his shift, until the end of the day.
        // When calculate shift and remove breaks duration from shift duration we get 720 singleManning Minutes for Wednesday 

    public function testScenarioThree()
    {
        $rota = new Rota();
        
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::WOLVERINE, new DateTime('next monday + 2 days 09:00:00'), new DateTime('next monday + 2 days 14:00:00')));
        $rota->addShift(new Shift(Shift::GAMORA, new DateTime('next monday + 2 days 10:00:00'), new DateTime('next monday + 2 days 17:00:00')));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 720,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);

        $result = $this->calculator->calculateSingleManning($rota); 
        $this->assertEquals($expected, $result);
    }


    // Scenario Four
        // Wolverine: |----|    |-----------------| From 'next thursday 09:00:00' to 'next thursday 13:00:00' with breaks ( From 'next thursday 09:00:00' to 'next thursday 13:00:00' )
        // Gamora:    |----------------|    |-----| From 'next thursday 09:00:00' to 'next thursday 13:00:00' with breaks ( From 'next thursday 09:00:00' to 'next thursday 13:00:00' )
        // Given Wolverine and Gamora working at FunHouse on Thursday
        // When Both of them work throughout the whole day
        // And The both have a lunch break each 
        // Then Wolverine receives single manning supplement while Gamorra is on break
        // And Gamorra receives single manning supplement during Wolverines break.
        // When calculate shift and remove breaks duration from shift duration we get 840 singleManning Minutes for Thursday 
    public function testScenarioFour()
    {
        $rota = new Rota();
        
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::WOLVERINE, new DateTime('next monday + 3 days 09:00:00'), new DateTime('next monday + 3 days 17:00:00'), [
            ['next monday + 3 days 10:30:00', 'next monday + 3 days 11:30:00'],
        ]));
        $rota->addShift(new Shift(Shift::GAMORA, new DateTime('next monday + 3 days 09:00:00'), new DateTime('next monday + 3 days 17:00:00'), [
            ['next monday + 3 days 14:30:00', 'next monday + 3 days 15:30:00'],
        ]));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 840,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);

        $result = $this->calculator->calculateSingleManning($rota); 

        $this->assertEquals($expected, $result);
    }
    

    // Scenario Five
    // Thor: |-----------------|    |---------|       From 'next friday 09:00:00' to 'next friday 15:00:00' with breaks ( From 'next friday 13:30:00' to 'next friday 14:30:00' )
    // Gamora:          |----------------|    |-----| From 'next friday 12:00:00' to 'next friday 17:00:00' with breaks ( From 'next friday 15:00:00' to 'next friday 16:00:00' )
    // Wolverine:   |-----|           |-------------| From 'next friday 11:00:00' to 'next friday 17:00:00' with breaks ( From 'next friday 12:30:00' to 'next friday 14:00:00' )

    // Given Thor, Gamora and Wolverine working at FunHouse on Friday
    // All of them work throughout the whole day
    // And All have a lunch break each 
    // When calculate shift and remove breaks duration from shift duration we get 810 singleManning Minutes for Friday 

    public function testScenarioFive()
    {
        $rota = new Rota();
        
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::THOR, new DateTime('next monday + 4 days 09:00:00'), new DateTime('next monday + 4 days 15:00:00'), [
            ['next monday + 4 days 13:30:00', 'next monday + 4 days 14:30:00'],
        ]));
        $rota->addShift(new Shift(Shift::GAMORA, new DateTime('next monday + 4 days 12:00:00'), new DateTime('next monday + 4 days 17:00:00'), [
            ['next monday + 4 days 15:00:00', 'next monday + 4 days 16:00:00'],
        ]));
        $rota->addShift(new Shift(Shift::WOLVERINE, new DateTime('next monday + 4 days 11:00:00'), new DateTime('next monday + 4 days 17:00:00'), [
            ['next monday + 4 days 12:30:00', 'next monday + 4 days 14:00:00'],
        ]));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 810,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);

        $result = $this->calculator->calculateSingleManning($rota); 

        $this->assertEquals($expected, $result);
    }


    // Scenario Six
    // Thor:                |---------|         |---------|         |---------| From 'next Saturday 11:00:00' to 'next Saturday 20:00:00' with breaks ( From 'next Saturday 13:00:00' to 'next Saturday 15:00:00' ) ( From 'next Saturday 17:00:00' to 'next Saturday 19:00:00' )
    // Wolverine:   |-------|         |---------|         |----------------|    From 'next Saturday 09:00:00' to 'next Saturday 19:00:00' with breaks ( From 'next Saturday 11:00:00' to 'next Saturday 13:00:00' ) ( From 'next Saturday 15:00:00' to 'next Saturday 17:00:00' )
    // Given Thor and Wolverine working at FunHouse on Saturday
    // Both of them work throughout the whole day
    // Both have two breaks in the whole shift
    // When calculate shift and remove breaks duration from shift duration we get 660 singleManning Minutes for Saturday 

    public function testScenarioSix()
    {
        $rota = new Rota();
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::THOR, new DateTime('next monday + 5 days 11:00:00'), new DateTime('next monday + 5 days 20:00:00'), [
            ['next monday + 5 days 13:00:00', 'next monday + 5 days 15:00:00'],
            ['next monday + 5 days 17:00:00', 'next monday + 5 days 19:00:00'],
        ]));
        $rota->addShift(new Shift(Shift::WOLVERINE, new DateTime('next monday + 5 days 09:00:00'), new DateTime('next monday + 5 days 19:00:00'), [
            ['next monday + 5 days 11:00:00', 'next monday + 5 days 13:00:00'],
            ['next monday + 5 days 15:00:00', 'next monday + 5 days 17:00:00'],
        ]));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 660,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);
        $result = $this->calculator->calculateSingleManning($rota); 

        $this->assertEquals($expected, $result);
    }


    // Scenario Seven
    // Thor:                |---------|         |---------|         |---------| From 'next Saturday 11:00:00' to 'next Saturday 20:00:00' with breaks ( From 'next Saturday 13:00:00' to 'next Saturday 15:00:00' ) ( From 'next Saturday 17:00:00' to 'next Saturday 19:00:00' )
    // Wolverine:   |      |---------|      |    From 'next Saturday 09:00:00' to 'next Saturday 19:00:00' with breaks ( From 'next Saturday 08:00:00' to 'next Saturday 20:00:00' )
    // Given Thor and Wolverine working at FunHouse on Saturday
    // Both of them work throughout the whole day
    // Thor have mulitple breaks within the shift, but Wolvarin have shift break start and end outside the shift 
    // When calculate shift and remove breaks duration from shift duration we get 300 singleManning Minutes for Saturday 

    public function testScenarioSeven()
    {
        $rota = new Rota();
        // Comming monday and onward week will be used for qouta
        $nextMonday =  new DateTime('next monday');

        // adding shifts to rota according to scenario
        $rota->addShift(new Shift(Shift::THOR, new DateTime('next monday + 5 days 11:00:00'), new DateTime('next monday + 5 days 20:00:00'), [
            ['next monday + 5 days 13:00:00', 'next monday + 5 days 15:00:00'],
            ['next monday + 5 days 17:00:00', 'next monday + 5 days 19:00:00'],
        ]));
        $rota->addShift(new Shift(Shift::WOLVERINE, new DateTime('next monday + 5 days 09:00:00'), new DateTime('next monday + 5 days 19:00:00'), [
            ['next monday + 5 days 08:00:00', 'next monday + 5 days 20:00:00'],
        ]));

        // The expected array will have 7 dates starting from comming monday from the current date
        $expected = new SingleManning([
            $nextMonday->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 300,
            $nextMonday->modify('+ 1 days')->format('Y-m-d') => 0,
            
        ]);
        $result = $this->calculator->calculateSingleManning($rota); 

        $this->assertEquals($expected, $result);
    }




    // Add more test methods for other scenarios or additional tests
}