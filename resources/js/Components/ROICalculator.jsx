import { useState, useMemo } from "react";
import { Link } from "@inertiajs/react";
import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

function formatNaira(amount) {
    return "₦" + Math.round(amount).toLocaleString("en-NG");
}

function AnimatedResult({ value, label, highlight, trigger }) {
    const count = useCountUp(Math.max(0, Math.round(value)), 1400, 0, trigger);

    const displayValue = label === "ROI"
        ? `${count}%`
        : label === "Payback Period"
        ? `${count} days`
        : formatNaira(count);

    return (
        <div
            className={`rounded-2xl p-4 border ${
                highlight
                    ? "bg-blue-600 text-white border-blue-600 col-span-2"
                    : "bg-white text-gray-900 border-gray-100"
            }`}
        >
            <p className={`text-xs font-medium mb-1 ${highlight ? "text-blue-100" : "text-gray-500"}`}>
                {label}
            </p>
            <p className={`text-2xl font-extrabold ${highlight ? "text-white" : "text-gray-900"}`}>
                {displayValue}
            </p>
        </div>
    );
}

export default function ROICalculator() {
    const [revenue, setRevenue] = useState(500000);
    const [missedCallsPct, setMissedCallsPct] = useState(40);
    const [avgOrderValue, setAvgOrderValue] = useState(5000);
    const [staffHours, setStaffHours] = useState(20);
    const [hourlyWage, setHourlyWage] = useState(1500);

    const plan = 35000;

    const results = useMemo(() => {
        const recoveredRevenue = (revenue * missedCallsPct) / 100;
        const laborSaved = staffHours * 4 * hourlyWage;
        const totalMonthlySavings = recoveredRevenue + laborSaved - plan;
        const annualSavings = totalMonthlySavings * 12;
        const roi = plan > 0 ? ((totalMonthlySavings / plan) * 100) : 0;
        const paybackDays = totalMonthlySavings > 0 ? Math.round((plan / totalMonthlySavings) * 30) : 0;
        return {
            monthlySavings: Math.max(0, totalMonthlySavings),
            recoveredRevenue,
            laborSaved,
            annualSavings: Math.max(0, annualSavings),
            roi: Math.max(0, roi),
            paybackDays,
        };
    }, [revenue, missedCallsPct, avgOrderValue, staffHours, hourlyWage]);

    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [calcRef, calcVisible] = useScrollAnimation(0.1);

    return (
        <section className="py-20 bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    ref={headingRef}
                    className="text-center mb-14"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transform: headingVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                >
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Calculate Your ROI
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        See exactly how much Blueflow can save your business every month
                    </p>
                </div>

                <div
                    ref={calcRef}
                    className="grid grid-cols-1 lg:grid-cols-2 gap-10 bg-gradient-to-br from-blue-50 to-emerald-50 rounded-3xl p-8 md:p-12 border border-blue-100"
                    style={{
                        opacity: calcVisible ? 1 : 0,
                        transform: calcVisible ? "translateY(0)" : "translateY(50px)",
                        transition: "opacity 0.7s ease, transform 0.7s ease",
                    }}
                >
                    {/* Inputs */}
                    <div>
                        <h3 className="font-bold text-gray-900 text-lg mb-6">Your Business Numbers</h3>
                        <div className="space-y-6">
                            <div>
                                <label className="text-sm font-medium text-gray-700 flex justify-between mb-1">
                                    <span>Monthly Sales Revenue</span>
                                    <span className="text-blue-700 font-semibold">{formatNaira(revenue)}</span>
                                </label>
                                <input type="range" min={50000} max={5000000} step={50000} value={revenue}
                                    onChange={(e) => setRevenue(Number(e.target.value))}
                                    className="w-full accent-blue-600 cursor-pointer" />
                                <div className="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>₦50K</span><span>₦5M</span>
                                </div>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 flex justify-between mb-1">
                                    <span>Estimated Missed Calls</span>
                                    <span className="text-blue-700 font-semibold">{missedCallsPct}%</span>
                                </label>
                                <input type="range" min={0} max={100} step={5} value={missedCallsPct}
                                    onChange={(e) => setMissedCallsPct(Number(e.target.value))}
                                    className="w-full accent-blue-600 cursor-pointer" />
                                <div className="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>0%</span><span>100%</span>
                                </div>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 flex justify-between mb-1">
                                    <span>Average Order Value</span>
                                    <span className="text-blue-700 font-semibold">{formatNaira(avgOrderValue)}</span>
                                </label>
                                <input type="range" min={500} max={100000} step={500} value={avgOrderValue}
                                    onChange={(e) => setAvgOrderValue(Number(e.target.value))}
                                    className="w-full accent-blue-600 cursor-pointer" />
                                <div className="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>₦500</span><span>₦100K</span>
                                </div>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 flex justify-between mb-1">
                                    <span>Staff Hours on Manual Tasks (per week)</span>
                                    <span className="text-blue-700 font-semibold">{staffHours} hrs</span>
                                </label>
                                <input type="range" min={1} max={80} step={1} value={staffHours}
                                    onChange={(e) => setStaffHours(Number(e.target.value))}
                                    className="w-full accent-blue-600 cursor-pointer" />
                                <div className="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>1 hr</span><span>80 hrs</span>
                                </div>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 flex justify-between mb-1">
                                    <span>Average Hourly Wage</span>
                                    <span className="text-blue-700 font-semibold">{formatNaira(hourlyWage)}</span>
                                </label>
                                <input type="range" min={500} max={10000} step={250} value={hourlyWage}
                                    onChange={(e) => setHourlyWage(Number(e.target.value))}
                                    className="w-full accent-blue-600 cursor-pointer" />
                                <div className="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>₦500</span><span>₦10K</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Results */}
                    <div>
                        <h3 className="font-bold text-gray-900 text-lg mb-6">Your Potential Savings</h3>
                        <div className="grid grid-cols-2 gap-4 mb-6">
                            {[
                                { label: "Monthly Savings", value: results.monthlySavings, highlight: true },
                                { label: "Recovered Revenue", value: results.recoveredRevenue },
                                { label: "Labor Saved", value: results.laborSaved },
                                { label: "Annual Savings", value: results.annualSavings },
                                { label: "ROI", value: results.roi },
                                { label: "Payback Period", value: results.paybackDays },
                            ].map((item) => (
                                <AnimatedResult
                                    key={item.label}
                                    value={item.value}
                                    label={item.label}
                                    highlight={item.highlight}
                                    trigger={calcVisible}
                                />
                            ))}
                        </div>

                        <p className="text-xs text-gray-400 mb-6">
                            Based on your numbers, Blueflow could save your business{" "}
                            <span className="text-blue-700 font-semibold">{formatNaira(results.annualSavings)}</span> per year.
                        </p>

                        <Link
                            href="/demo"
                            className="w-full block text-center bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-colors"
                        >
                            Get Your Custom Quote
                        </Link>
                        <p className="text-xs text-center text-gray-400 mt-3">
                            * Results are estimates based on industry averages. Actual results may vary.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
