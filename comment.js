#!/usr/bin/env node

const bot = require("./module/circle-github-bot").create();

bot.comment(`
<h3>${bot.env.commitMessage}</h3>
Component diffs: <strong>${bot.artifactLink('app/build/summary.html', 'Summary')}</strong>
`);