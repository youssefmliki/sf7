## Testing Best Practices and Strategies

### 1. The Testing Pyramid
- **Unit tests (base)**: Most numerous, fastest. Validate small units (functions, methods) in isolation.
- **Integration tests (middle)**: Fewer. Validate interactions between components (e.g., DB, HTTP, filesystem).
- **End-to-End tests (top)**: Fewest. Validate critical user journeys through the full stack.

### 2. When to Use Each Test Type
- **Unit**: Pure logic, algorithms, services. Mock/stub I/O or collaborators.
- **Integration**: Controllers, repositories, message handlers; verify framework wiring and persistence.
- **E2E**: Happy paths and business-critical flows only; keep minimal to avoid flakiness.

### 3. Practical Guidelines
- **TDD where possible**: Write tests first for new features.
- **Coverage target**: Aim for 70–80% line coverage; prioritize complex/high-risk code.
- **Fast and independent**: No hidden order dependencies; reset state between tests.
- **Descriptive names**: e.g., `testUserCanLoginWithValidCredentials`.
- **Deterministic data**: Use fixtures/factories; avoid time/randomness or control them.
- **CI-first mindset**: Run tests locally the same way as CI; include coverage in pipelines.

### 4. Symfony-Specific Best Practices
- **Controllers**: Use `WebTestCase` to exercise routes and responses.
- **Fixtures**: Use DoctrineFixturesBundle or test factories to seed consistent data.
- **Mocking services**: Override services in the test container; use `$client->getContainer()` to replace implementations.
- **HTTP-level assertions**: `assertResponseIsSuccessful`, `assertSelectorTextContains`, `assertPageTitleContains`.
- **E2E**: Prefer Panther (when compatible) for browser flows; otherwise `WebTestCase` for HTTP-level integration.

### 5. Example Classifications
- **Unit**: `App\Service\Calculator::add()` tested with `PHPUnit\Framework\TestCase`.
- **Integration**: Controller test via `WebTestCase` making HTTP requests to the kernel.
- **E2E**: Panther-driven browser test simulating user interactions end-to-end.

### 6. Coverage
- Generate HTML coverage locally:
  - `php bin/phpunit --coverage-html=build/coverage`
- In CI, publish coverage artifacts and enforce thresholds on critical packages.






