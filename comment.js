#!/usr/bin/env node

const bot = require("./third-party/circle-github-bot-release-1.0.0").create();

bot.comment(`
<h3>${bot.env.commitMessage}</h3>
Component diffs: <strong>${bot.artifactLink('app/build/summary.html', 'Summary')}</strong>
`);