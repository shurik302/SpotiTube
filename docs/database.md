# Database Blueprint (Draft)

## Target Engine
- MySQL 8.0+

## Core Schemas (initial draft)
| Table | Purpose |
|-------|---------|
| users | Local accounts, OAuth links, roles |
| user_providers | OAuth identities (e.g., Google) |
| artists | Performer catalog |
| albums | Album metadata linked to artists |
| tracks | Individual tracks with streaming URIs |
| track_assets | Storage info per CDN/source |
| ratings | User ratings for artists/albums/tracks |
| playlists | User playlists/favorites |
| playlist_tracks | Junction table |
| recommendations | Cached personalized recs |
| admin_audit_logs | CMS actions |

Detailed schema with columns, constraints, and indexing strategy will follow once DB reset is confirmed.
