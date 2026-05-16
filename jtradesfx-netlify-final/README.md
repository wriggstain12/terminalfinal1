# JtradesFX — Netlify Deployment

## Deploy in 4 steps (5 minutes)

### 1. Go to Netlify
https://app.netlify.com — sign up free with GitHub

### 2. Import your repo
- Click "Add new site" → "Import an existing project"
- Click "GitHub" → select your `jtradesfx` repo
- Build command: (leave blank)
- Publish directory: `.`  (just a dot)
- Click "Deploy site"

### 3. Add environment variables
Site settings → Environment variables → Add variable:

| Key                    | Value                                    |
|------------------------|------------------------------------------|
| EXCHANGERATE_API_KEY   | 6c30cb730645e2f502c71d4f                |
| FRED_API_KEY           | bc015122c7283bcbcf19d2184a5dadeb        |
| NEWS_API_KEY           | 80b555adc7714ecbab2017343a85abce        |
| ANTHROPIC_API_KEY      | sk-ant-... from console.anthropic.com   |

### 4. Trigger redeploy
Deploys → Trigger deploy → Deploy site

Live at https://your-site.netlify.app
