# Remote Theme Management (Git Updater)

The [Git Remote Updater](https://git-updater.com/git-remote-updater/) plugin was created to simplify remote management of Git Updater–supported plugins and themes. You need the **Site URL** and **REST API key** for Git Remote Updater settings (or other tools: MainWP, ManageWP, InfiniteWP, iThemes Sync, custom webhooks).

> See the [Git Updater Knowledge Base](https://git-updater.com/knowledge-base/) for the full list of attributes and advanced usage.

---

## Site & API Key

| Setting | Value |
|--------|--------|
| **Site URL** | https://temp6.devigontech.com |
| **REST API key** | `4482d949aedba30fcfc5898facb5c63c` |

Use the **Site URL** and **REST API key** in your remote management tool (for example, in the settings for Git Remote Updater or a custom webhook integration).

> **Security:** Anyone with this API key can trigger theme/plugin updates on the site. Do not expose it in client-side code. If this repository is ever made public, move the key into a secure secrets manager and remove it from version control.

---

## REST API Endpoints

Base path: `https://temp6.devigontech.com/wp-json/git-updater/v1/`  
All endpoints require the query parameter: `key=4482d949aedba30fcfc5898facb5c63c`

### Update (webhook / remote update)

**Endpoint base:**  
`https://temp6.devigontech.com/wp-json/git-updater/v1/update/?key=4482d949aedba30fcfc5898facb5c63c`

Append standard Git Updater query parameters such as:

- `theme=` – the theme slug to update
- `branch=`, `tag=`, `committish=` – which version/branch to deploy
- `override=` – whether to override the stored branch/tag

Example (curl, updating this theme by slug — matches `Text Domain` in `style.css`):

```bash
curl "https://temp6.devigontech.com/wp-json/git-updater/v1/update/?key=4482d949aedba30fcfc5898facb5c63c&theme=free-backlinks-generator"
```

### Reset branch

Use if the theme is stuck on a deleted branch and Git Updater can’t connect.

**Endpoint base:**  
`https://temp6.devigontech.com/wp-json/git-updater/v1/reset-branch/?key=4482d949aedba30fcfc5898facb5c63c`

Example (reset this theme’s branch — ensure the `theme=` value matches the theme directory slug):

```bash
curl "https://temp6.devigontech.com/wp-json/git-updater/v1/reset-branch/?key=4482d949aedba30fcfc5898facb5c63c&theme=free-backlinks-generator"
```

---

## References

- [Git Updater Knowledge Base](https://git-updater.com/knowledge-base/)
- [Required Headers](https://git-updater.com/knowledge-base/required-headers/)
- [Remote Management – REST API Endpoints](https://git-updater.com/knowledge-base/remote-management-restful-endpoints/)
- [Versions & Branches](https://git-updater.com/knowledge-base/versions-branches/)
- [Git Remote Updater plugin](https://git-updater.com/git-remote-updater/)
