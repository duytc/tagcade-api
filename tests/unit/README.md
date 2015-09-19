# Unit test for Replicator Service
## 1. Data to test
- before each test, data from tests/_data/replicator_unit_test.sql will be restored to database
- after each test, database will be cleaned up and recovered as nothing happened
- all modules configuration reside in unit.suite.yml
- each added test function must have '@test' annotation to be run accompanied with units