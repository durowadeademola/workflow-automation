import { Head } from "@inertiajs/react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";

export default function TermsOfService() {
    return (
        <MainLayout>
            <Head title="Terms of Service — Blueflow Automation">
                <meta name="description" content="The terms that govern your use of Blueflow Automation's website and services." />
            </Head>

            <PageHero title="Terms of Service" subtitle="Last updated: July 9, 2026" />

            <section className="py-16 bg-white">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="space-y-10 text-gray-600 leading-relaxed text-sm sm:text-base">
                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">1. Acceptance of Terms</h2>
                            <p>
                                By accessing this website or engaging Blueflow Automation for automation services,
                                you agree to be bound by these Terms of Service. If you do not agree, please do not
                                use our website or services.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">2. Our Services</h2>
                            <p>
                                Blueflow Automation designs and implements business automation — including WhatsApp
                                automation, CRM integration, email and payment automation, and custom workflow
                                automation using tools such as n8n. Specific deliverables, timelines, and pricing for
                                any engagement are agreed separately with each client before work begins.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">3. Client Responsibilities</h2>
                            <p>
                                To deliver our services, we may require access to your business tools and accounts
                                (e.g. WhatsApp Business, payment gateways, CRMs). You are responsible for ensuring
                                you have the right to grant us this access and for the accuracy of information you
                                provide to us.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">4. Payment &amp; Cancellation</h2>
                            <p>
                                Subscription plans are billed monthly unless otherwise agreed in writing. You may
                                cancel a subscription at any time; cancellation takes effect at the end of the
                                current billing period, and no further payments will be collected. Fees already
                                paid for a billing period already in progress are non-refundable, except where
                                required by law.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">5. Acceptable Use</h2>
                            <p>
                                You agree not to use our services to send unsolicited spam, engage in fraudulent
                                activity, or violate WhatsApp's, Meta's, or any other integrated platform's terms of
                                service. We reserve the right to suspend service for accounts found in violation.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">6. Limitation of Liability</h2>
                            <p>
                                We work to keep the systems we build reliable, but we do not guarantee uninterrupted
                                or error-free operation. To the maximum extent permitted by law, Blueflow Automation
                                is not liable for indirect or consequential losses arising from the use of, or
                                inability to use, our services.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">7. Changes to These Terms</h2>
                            <p>
                                We may update these terms from time to time. Continued use of our website or
                                services after changes are posted constitutes acceptance of the updated terms.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">8. Governing Law</h2>
                            <p>
                                These terms are governed by the laws of the Federal Republic of Nigeria.
                            </p>
                        </div>

                        <div>
                            <h2 className="text-xl font-bold text-gray-900 mb-3">9. Contact Us</h2>
                            <p>
                                Questions about these terms can be sent to{" "}
                                <a href="mailto:hello@blueflowautomation.com" className="text-blue-700 font-semibold hover:underline">
                                    hello@blueflowautomation.com
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
