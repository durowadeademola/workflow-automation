import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const testimonials = [
    {
        quote: "Blueflow transformed our business overnight. We went from missing half our customer calls to capturing every single inquiry. The ROI was immediate and massive.",
        author: "Babatunde Adebayo",
        role: "Owner, Mama's Kitchen",
        location: "Lagos",
        rating: 5,
        initial: "B",
    },
    {
        quote: "I was skeptical at first, but within 2 weeks we saw a 40% jump in completed orders. The WhatsApp automation handles hundreds of customer inquiries daily without any staff.",
        author: "Ngozi Okonkwo",
        role: "CEO, StyleHaven",
        location: "Abuja",
        rating: 5,
        initial: "N",
    },
    {
        quote: "Our hotel occupancy went from 60% to 85% in just one quarter. The automated booking reminders alone have saved us thousands in lost reservations.",
        author: "Emeka Nwosu",
        role: "General Manager, Lekki Suites",
        location: "Lagos",
        rating: 5,
        initial: "E",
    },
    {
        quote: "Patient no-shows dropped by over 50% after we deployed the reminder system. Our clinic runs smoother than ever and our staff are less stressed.",
        author: "Dr. Amaka Obi",
        role: "Medical Director, CareFirst Clinics",
        location: "Port Harcourt",
        rating: 5,
        initial: "A",
    },
    {
        quote: "As a real estate agent, following up with every lead manually was impossible. Now Blueflow does it for me. My close rate has more than doubled.",
        author: "Tunde Fashola",
        role: "Principal Agent, PrimeProp",
        location: "Abuja",
        rating: 5,
        initial: "T",
    },
    {
        quote: "The automation saved our small team about 25 hours a week. That's time we now spend growing the business instead of answering the same questions over and over.",
        author: "Chioma Eze",
        role: "Founder, Zeemade Bakery",
        location: "Enugu",
        rating: 5,
        initial: "C",
    },
];

function Stars({ count = 5 }) {
    return (
        <div className="flex gap-0.5 mb-3">
            {Array.from({ length: count }).map((_, i) => (
                <svg key={i} className="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            ))}
        </div>
    );
}

export default function Testimonials() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.05);

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
                        What Our Clients Say
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        Join 100+ Nigerian businesses that have transformed their operations with Blueflow
                    </p>
                </div>

                <div ref={cardsRef} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {testimonials.map((t, i) => (
                        <div
                            key={t.author}
                            className="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-md transition-shadow flex flex-col"
                            style={{
                                opacity: cardsVisible ? 1 : 0,
                                transform: cardsVisible ? "translateY(0)" : "translateY(50px)",
                                transition: `opacity 0.6s ease ${i * 90}ms, transform 0.6s ease ${i * 90}ms`,
                            }}
                        >
                            <Stars />
                            <p className="text-gray-700 text-sm leading-relaxed italic flex-1 mb-5">
                                "{t.quote}"
                            </p>
                            <div className="flex items-center gap-3">
                                <div className="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {t.initial}
                                </div>
                                <div>
                                    <p className="font-semibold text-gray-900 text-sm">{t.author}</p>
                                    <p className="text-xs text-gray-500">{t.role} • {t.location}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
