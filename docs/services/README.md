# Service and Endpoint Reference

The client exposes the following typed services. Select a service to see its
implemented endpoints, request and response types, exceptions, and examples.

| Service | Client accessor | Scope |
| --- | --- | --- |
| [Applications](applications.md) | `applications()` | Application lifecycle operations. |
| [Environments](environments.md) | `environments()` | Application environment operations. |
| [Domains](domains.md) | `domains()` | Custom domain and DNS verification operations. |
| [Commands](commands.md) | `commands()` | Remote command execution operations. |
| [Deployments](deployments.md) | `deployments()` | Application deployment operations. |
| [Instances](instances.md) | `instances()` | Compute instance operations. |
| [Background Processes](background-processes.md) | `backgroundProcesses()` | Background process operations. |
| [Database Clusters](database-clusters.md) | `databaseClusters()` | Database cluster operations. |
| [Databases](databases.md) | `databases()` | Database operations. |
| [Database Snapshots](database-snapshots.md) | `databaseSnapshots()` | Database snapshot operations. |
| [Database Restores](database-restores.md) | `databaseRestores()` | Database restore operations. |
| [Object Storage Buckets](object-storage-buckets.md) | `objectStorageBuckets()` | Object storage bucket operations. |
| [Bucket Keys](bucket-keys.md) | `bucketKeys()` | Object storage bucket credential operations. |
| [Caches](caches.md) | `caches()` | Managed cache operations. |
| [WebSocket Clusters](websocket-clusters.md) | `webSocketClusters()` | WebSocket cluster operations. |
| [WebSocket Applications](websocket-applications.md) | `webSocketApplications()` | WebSocket application operations. |
| [Dedicated Clusters](dedicated-clusters.md) | `dedicatedClusters()` | Dedicated infrastructure cluster operations. |
| [Edge Networks](edge-networks.md) | `edgeNetworks()` | Edge network operations. |
| [Secrets](secrets.md) | `secrets()` | Environment secret operations. |
| [Usage](usage.md) | `usage()` | Usage and billing-data operations. |
| [Meta](meta.md) | `meta()` | Laravel Cloud API metadata operations. |
| [Legacy Databases](legacy-databases.md) | `legacyDatabases()` | Legacy database operations exposed by the official API. |

An empty endpoint table means the service is wired into the client but its
official endpoint contract has not yet been implemented.
