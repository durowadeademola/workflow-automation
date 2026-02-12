import { Link } from '@inertiajs/react'

export default function Navbar() {
    return (
        <header className="sticky top-0 bg-white/80 backdrop-blur-md border-b z-50">
            <div className="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <h1 className="text-2xl font-bold text-blue-600">
                    Blueflow
                </h1>

                <nav className="hidden md:flex gap-8 text-sm font-medium">
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#how">How it works</a>
                </nav>

                <div className="flex gap-4">
                    <Link href="/login" className="text-sm font-medium">
                        Login
                    </Link>
                    <Link href="/register" className="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        Get Started
                    </Link>
                </div>
            </div>
        </header>
    )
}
