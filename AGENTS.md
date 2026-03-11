# Lapisense PHP Client

- Treat the public classes in `src/`, `README.md` examples, and tests as the package contract.
- Keep this package framework-agnostic. WordPress-specific behavior belongs in `wordpress-client`, not here.
- Preserve the transport abstraction around `HttpClientInterface`; avoid coupling `ApiClient` to a concrete HTTP implementation.
- Treat public client methods and payload shapes as a stable API. Update tests and `README.md` whenever that surface changes.
- Run tests and quality tools through the container defined in `docker-compose.yml`, not the host environment.
