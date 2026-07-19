import { Head } from "@inertiajs/react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";

export default function PrivacyPolicy() {
    return (
        <MainLayout>
            <Head title="Privacy Policy — Blueflow Automation">
                <meta name="description" content="How Blueflow Automation collects, uses, and protects your data." />
            </Head>

            <PageHero title="Privacy Policy" subtitle="Last updated: July 9, 2026" />

            <section className="py-16 bg-white dark:bg-gray-900">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-blue">
                    <div className="space-y-10 text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">1. Information We Collect</h2>
                            <p>
                                We collect information you provide directly to us, such as when you fill out our
                                contact form, message us on WhatsApp, or interact with an AI chat widget we've built
                                for a client's website. This may include your name, business name, email address,
                                phone number, and the content of your messages. If you use one of our clients'
                                automated ordering or booking systems, we may also process order details, delivery
                                information, and payment references on their behalf.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">2. How We Use Your Information</h2>
                            <p>
                                We use the information we collect to respond to inquiries, provide and improve our
                                automation services, operate the systems we build for our clients, and communicate
                                with you about your account or request. We do not sell your personal information to
                                third parties.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">3. Data Sharing</h2>
                            <p>
                                We share data only with the service providers necessary to operate our platform —
                                for example, our hosting provider, payment processors (such as Paystack or
                                Flutterwave), and messaging providers (such as WhatsApp Business API). Where we
                                build automation on behalf of a client business, relevant customer data (such as an
                                order or booking) is shared with that business so they can fulfil it.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">4. Cookies</h2>
                            <p>
                                Our website uses minimal cookies required for the site to function correctly (such
                                as session cookies). We do not use third-party advertising trackers. If this
                                changes, we will update this policy.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">5. Data Security</h2>
                            <p>
                                We take reasonable technical and organisational measures to protect your data,
                                including encrypted connections (SSL/TLS) and access controls on our systems.
                                No method of transmission or storage is 100% secure, but we work to protect your
                                information to industry standards.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">6. Your Rights</h2>
                            <p>
                                You may request access to, correction of, or deletion of your personal data at any
                                time by contacting us at{" "}
                                <a href="mailto:hello@blueflowautomation.com" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                    hello@blueflowautomation.com
                                </a>. We will respond within a reasonable timeframe.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">7. Contact Us</h2>
                            <p>
                                If you have any questions about this Privacy Policy, reach us at{" "}
                                <a href="mailto:hello@blueflowautomation.com" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                    hello@blueflowautomation.com
                                </a>{" "}
                                or via WhatsApp at{" "}
                                <a href="https://wa.me/2347064706193" target="_blank" rel="noreferrer" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                    +234 706 470 6193
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
