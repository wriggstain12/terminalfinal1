# JtradesFX Terminal — IONOS Deploy Now

## Deploy in 5 steps

### 1. Push to GitHub

1. Go to https://github.com/new — create a repo named `jtradesfx`
2. Click "uploading an existing file"
3. Upload ALL files from this folder (including the `api/` folder and `.htaccess`)
4. Commit

### 2. Connect IONOS Deploy Now

1. Log in to your IONOS account
2. Go to **Hosting → Deploy Now**
3. Click **New Project → Deploy from my own GitHub repository**
4. Select your `jtradesfx` repo
5. Settings:
   - **Script language**: PHP
   - **Build command**: (leave blank)
   - **Dist folder**: `/` (just a forward slash)
6. Click **Deploy**

### 3. Add API keys as runtime variables

In your Deploy Now project → **Runtime Configuration → Add variable**:

| Variable | Value |
|---|---|
| `EXCHANGERATE_API_KEY` | `6c30cb730645e2f502c71d4f` |
| `FRED_API_KEY` | `bc015122c7283bcbcf19d2184a5dadeb` |
| `NEWS_API_KEY` | `80b555adc7714ecbab2017343a85abce` |
| `ANTHROPIC_API_KEY` | your key from console.anthropic.com |

### 4. Redeploy

Click **Trigger Deployment** after adding the variables.

### 5. Connect your domain (optional)

Go to **Domains** in your Deploy Now project and add your IONOS domain.

---

## How it works

Every API call from the terminal goes to a PHP file on your IONOS server:

| URL | File | What it does |
|---|---|---|
| `/api/fx` | `api/fx.php` | Live FX rates via ExchangeRate-API |
| `/api/fred` | `api/fred.php` | FRED macro data (CPI, NFP, GDP etc.) |
| `/api/news` | `api/news.php` | Live forex headlines via NewsAPI |
| `/api/claude` | `api/claude.php` | Claude AI for briefings, signals, chat |
| `/api/yields` | `api/yields.php` | US Treasury yield curve |
| `/api/cot` | `api/cot.php` | CFTC COT positioning data |

Your API keys live only on the IONOS server — never in the browser.

---

## Making changes

Edit `index.html` on GitHub → Deploy Now auto-deploys within 60 seconds.
