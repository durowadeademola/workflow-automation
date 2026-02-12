export default function Footer() {
    return (
        <footer className="bg-gray-900 text-gray-400 py-12">
            <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-8">
                <div>
                    <h3 className="text-white text-xl font-bold">BlueFlow</h3>
                    <p className="mt-4">
                        Intelligent workflow automation for African businesses.
                    </p>
                </div>

                <div>
                    <h4 className="text-white font-semibold mb-4">Company</h4>
                    <ul className="space-y-2">
                        <li>About</li>
                        <li>Pricing</li>
                        <li>Contact</li>
                    </ul>
                </div>

                <div>
                    <h4 className="text-white font-semibold mb-4">Legal</h4>
                    <ul className="space-y-2">
                        <li>Privacy Policy</li>
                        <li>Terms</li>
                    </ul>
                </div>
            </div>

            <div className="text-center mt-12 text-sm">
                © {new Date().getFullYear()} BlueFlow. All rights reserved.
            </div>
        </footer>
    )
}
