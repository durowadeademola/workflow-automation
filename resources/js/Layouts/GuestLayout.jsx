import Navbar from '../Components/Navbar'
import Footer from '../Components/Footer'

export default function GuestLayout({ children }) {
    return (
        <div className="bg-white text-gray-900">
            <Navbar />
            {children}
            <Footer />
        </div>
    )
}
