import { Head } from '@inertiajs/react';
import GuestLayout from '../Layouts/GuestLayout'
import Hero from '../Components/Hero'
import Features from '../Components/Features'
import HowItWorks from '../Components/HowItWorks'
import Pricing from '../Components/Pricing'
import CTA from '../Components/CTA'

export default function Home() {
    return (
        <>
        <Head title="Automate Your Businesses" />
        
            <GuestLayout>
                <Hero />
                <Features />
                <HowItWorks />
                <Pricing />
                <CTA />
            </GuestLayout>
        </>
    )
}
