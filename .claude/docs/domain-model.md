# Domain model

Every value object is a string value object (`AbstractStringValueObject`) with the compact canonical form of its identifier.
Exceptions will be `Identifiers\Exception\Invalid*Exception` (messages never carry the input). The per-class rules, with their
sources, are in `docs/sources/IDENTIFIERS.md` ("Outcome per class"). Registry data decodes and never refuses.

No class exists yet: Task 1 of the wave plan delivers the sources and the helpers.
