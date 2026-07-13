import { Head } from "@inertiajs/react";
import Navbar from "@/Components/Navbar";
import Hero from "@/Components/Hero";
import PainPoints from "@/Components/PainPoints";
import Solutions from "@/Components/Solutions";
import Services from "@/Components/Services";
import Industries from "@/Components/Industries";
import HowItWorks from "@/Components/HowItWorks";
import CaseStudies from "@/Components/CaseStudies";
import Testimonials from "@/Components/Testimonials";
import Pricing from "@/Components/Pricing";
import ROICalculator from "@/Components/ROICalculator";
import TrustSignals from "@/Components/TrustSignals";
import FAQ from "@/Components/FAQ";
import CTABanner from "@/Components/CTABanner";
import Footer from "@/Components/Footer";

export default function Home({ plans }) {
    return (
        <>
            <Head title="Blueflow Automation - AI Automation for Nigerian Businesses">
                <meta
                    name="description"
                    content="Transform your business operations with AI-powered automation. Save time, reduce costs, and scale effortlessly. Built for Nigerian businesses."
                />
            </Head>

            <div className="min-h-screen bg-white">
                <Navbar />

                <main>
                    <Hero />
                    <PainPoints />
                    <Solutions />
                    <Services />
                    <Industries />
                    <HowItWorks />
                    <CaseStudies />
                    <Testimonials />
                    <Pricing plans={plans} />
                    <ROICalculator />
                    <TrustSignals />
                    <FAQ />
                    <CTABanner />
                </main>

                <Footer />
            </div>
        </>
    );
}
