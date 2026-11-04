// Cross-check: plugin math must equal the Sheet & Flow MCP server math for random inputs in documented ranges.
'use strict';
const assert = require('assert');
const path = require('path');
const plug = require(path.join(__dirname, '../sheet-flow-rental-property-calculator/assets/calc.js'));
const ref = require(process.env.MCP_CALC || path.join(__dirname, '../../sheet-and-flow-mcp/server/calc.js'));
let seed = 12345; const rnd = () => (seed = (seed * 1103515245 + 12345) % 2147483648) / 2147483648;
const u = (a, b) => a + (b - a) * rnd();
function same(a, b, label) {
  for (const k of Object.keys(b)) {
    const x = a[k], y = b[k];
    if (y === null || x === null) { assert.strictEqual(x, y, label + ' ' + k); continue; }
    assert.ok(Math.abs(x - y) <= 1e-9 * Math.max(1, Math.abs(y)), `${label} ${k}: ${x} vs ${y}`);
  }
}
let n = 0;
for (let t = 0; t < 2000; t++) {
  const i = { price: u(50e3, 2e6), downPct: [0, 0.035, 0.2, 0.25, 1][t % 5] || u(0, 1), rate: t % 7 === 0 ? 0 : u(0.02, 0.12), years: [15, 20, 30][t % 3],
    units: 1 + (t % 4), rent: u(500, 5000), otherIncome: u(0, 300), vacancyPct: u(0, 0.15), taxes: u(0, 15000), insurance: u(0, 5000),
    hoa: u(0, 400), utilities: u(0, 300), otherExp: u(0, 100), repairsPct: u(0, 0.1), capexPct: u(0, 0.1), mgmtPct: u(0, 0.12),
    closing: u(0, 20000), repairs: u(0, 50000), pointsPct: u(0, 0.02) };
  same(plug.analyzeRental(i), ref.analyzeRental(i), 'ltr#' + t); n++;
  const p = { price: u(80e3, 1.5e6), downPct: u(0.1, 1), rate: t % 9 === 0 ? 0 : u(0.02, 0.12), years: 30, occupancy: u(0, 1), adr: u(50, 800),
    blockedNights: Math.floor(u(0, 60)), avgStayNights: t % 11 === 0 ? 0 : u(1, 7), cleaningFee: u(0, 250), cleaningCost: u(0, 200),
    hostFeePct: u(0, 0.15), mgmtPct: u(0, 0.25), maintCapexPct: u(0, 0.1), fixedMonthly: u(0, 1500), closing: u(0, 20000), furnishing: u(0, 40000) };
  same(plug.analyzeStr(p), ref.analyzeStr(p), 'str#' + t); n++;
}
// Fixed check: $200k, 25% down, 7%/30y, $1,800 rent -> P&I 997.95 (standard amortization).
const r = plug.analyzeRental({ price: 200000, downPct: 0.25, rate: 0.07, years: 30, units: 1, rent: 1800 });
assert.strictEqual(r.monthlyPayment.toFixed(2), '997.95');
assert.strictEqual(r.capRate.toFixed(4), (21600 / 200000).toFixed(4));
console.log(`ok ${n} random cross-checks + 2 fixed checks`);
