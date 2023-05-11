# Single Manning Calculator
The Single Manning Calculator is a PHP project that calculates the single manning supplement for shifts in a work roster. It provides a flexible and extensible solution for determining the periods when an employee is working alone, based on the shifts scheduled in the roster.

## How it Works
The Single Manning Calculator project is built using PHP and follows an object-oriented approach. It leverages the powerful DateTime class provided by PHP for accurate date and time calculations. The project consists of the following key components:

__Shift Class:__ This class represents a single shift in the work roster. It encapsulates information such as the name of the employee working the shift, the start time, end time, and any breaks during the shift. The Shift class provides methods to set and retrieve shift details.

__Rota Class:__ The Rota class manages a collection of shifts in the work roster. It provides methods to add shifts to the roster and maintains an internal data structure to store the shifts. The Rota class allows for easy manipulation of the roster and provides convenient methods to retrieve shift details.

__SingleManningCalculator Class:__ The SingleManningCalculator class is responsible for calculating the single manning periods based on the shifts in the roster. It takes a Rota instance as input and performs the necessary calculations to determine the intervals when an employee is working alone. The SingleManningCalculator class applies date and time comparisons to identify single manning periods and calculates the corresponding single manning minutes for each day.

__SingleManning Class:__ The SingleManning class represents the result of the single manning calculation. It stores the total single manning minutes for each day in the roster. The SingleManning class provides methods to set and retrieve the single manning minutes for specific dates.

The project uses the PHPUnit testing framework to implement unit tests. The provided test cases in the SingleManningCalculatorTest class demonstrate the expected behavior of the Single Manning Calculator for different scenarios. You can run the test cases using PHPUnit to verify the correctness of the implementation and adapt them to your specific use cases.

The codebase is organized into separate classes, each responsible for a specific aspect of the functionality. This modular design allows for easy extension and customization. You can explore the codebase to understand the implementation details and make modifications as needed for your requirements.

## Getting Started

#### To get started with the Single Manning Calculator, follow these steps:
1. Clone the repository to your local machine.
2. Make sure you have PHP and Composer installed.
3. Run composer install to install the project dependencies (PhpUnit).
4. Modify the test cases in the SingleManningCalculatorTest class according to your requirements.
5. Run the test cases with the  `vendor/bin/phpunit tests/SingleManningCalculatorTest.php` Command.
6. Explore the codebase and customize it as needed for your specific use case.

#### To use the Single Manning Calculator, follow these steps:
1. Create a new instance of the Rota class.
2. Add shifts to the roster using the addShift method, providing the necessary shift details.
3. Create an instance of the SingleManningCalculator class.
4. Call the calculateSingleManning method, passing the Rota instance as a parameter.
5. The result will be an instance of the SingleManning class, which contains the total single manning minutes for each day in the roster.

### Notes
1. In the SingleManningCalculatorTest class the dates are automatically fetched from system dates. All days of the week are in test cases from the next monday. The expected 7 dates are as well started from the next monday.
2. We get the next monday date according to system date and then make rota for one week starting from that date.


### Aditional Scenarios
The following are `additional scenarios` which covers some of `extra cases` for the code.e.g: `multiple breaks`, `more than two persons` and the `seventh scenario is where the breaks starts and ends outside the shift duration in that case the shift wont be count.`
#### Scenario Five
```
Thor: |-----------------|    |---------|       From 'next friday 09:00:00' to 'next friday 15:00:00' with breaks ( From 'next friday 13:30:00' to 'next friday 14:30:00' )
Gamora:          |----------------|    |-----| From 'next friday 12:00:00' to 'next friday 17:00:00' with breaks ( From 'next friday 15:00:00' to 'next friday 16:00:00' )
Wolverine:   |-----|           |-------------| From 'next friday 11:00:00' to 'next friday 17:00:00' with breaks ( From 'next friday 12:30:00' to 'next friday 14:00:00' )
```
__Given__ Thor, Gamora and Wolverine working at FunHouse on Friday
__When__ All of them work throughout the whole day
__And__ All have `a lunch break each` 
__Result__ When calculate shift and remove breaks duration from shift duration we get 810 singleManning Minutes for Friday 

#### Scenario Six
```
Thor:                |---------|         |---------|         |---------| From 'next Saturday 11:00:00' to 'next Saturday 20:00:00' with breaks ( From 'next Saturday 13:00:00' to 'next Saturday 15:00:00' ) ( From 'next Saturday 17:00:00' to 'next Saturday 19:00:00' )
Wolverine:   |-------|         |---------|         |----------------|    From 'next Saturday 09:00:00' to 'next Saturday 19:00:00' with breaks ( From 'next Saturday 11:00:00' to 'next Saturday 13:00:00' ) ( From 'next Saturday 15:00:00' to 'next Saturday 17:00:00' )
```
__Given__ Thor and Wolverine working at FunHouse on Saturday
__When__ Both of them work throughout the whole day
__And__  Both `have two breaks` in the whole shift
__Result__ When calculate shift and remove breaks duration from shift duration we get 660 singleManning Minutes for Saturday 


#### Scenario Seven
```
Thor:                |---------|         |---------|         |---------| From 'next Saturday 11:00:00' to 'next Saturday 20:00:00' with breaks ( From 'next Saturday 13:00:00' to 'next Saturday 15:00:00' ) ( From 'next Saturday 17:00:00' to 'next Saturday 19:00:00' )
Wolverine:   |      |---------|      |    From 'next Saturday 09:00:00' to 'next Saturday 19:00:00' with breaks ( From 'next Saturday 08:00:00' to 'next Saturday 20:00:00' )
```
__Given__ Thor and Wolverine working at FunHouse on Saturday
__When__ Both of them work throughout the whole day
__And__ Thor `have mulitple breaks` within the shift, but `Wolvarin have shift break start and end outside the shift` 
__Result__ When calculate shift and remove breaks duration from shift duration we get 300 singleManning Minutes for Saturday 

