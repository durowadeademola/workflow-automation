export default function Hero() {
    return (
        <section className="relative bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
            <div className="max-w-7xl mx-auto px-6 py-28 text-center">
                <h1 className="text-5xl md:text-6xl font-bold leading-tight">
                    Automate Your Business  
                    <span className="block">With Intelligent Workflows</span>
                </h1>

                <p className="mt-6 text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                    BlueFlow helps African businesses automate sales, CRM, 
                    messaging, and operations — all from one dashboard.
                </p>

                <div className="mt-8 flex justify-center gap-6">
                    <button className="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold">
                        Start Free Trial
                    </button>
                    <button className="border border-white px-6 py-3 rounded-xl">
                        Watch Demo
                    </button>
                </div>
            </div>
        </section>
    )
}
