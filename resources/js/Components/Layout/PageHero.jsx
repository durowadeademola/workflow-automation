export default function PageHero({ badge, title, subtitle }) {
    return (
        <section className="relative bg-gradient-to-br from-blue-50 via-white to-emerald-50 pt-32 pb-16 overflow-hidden">
            <div className="absolute top-10 right-0 w-72 h-72 bg-blue-100 rounded-full opacity-40 blur-3xl -z-0" />
            <div className="absolute bottom-0 left-0 w-72 h-72 bg-emerald-100 rounded-full opacity-30 blur-3xl -z-0" />

            <div className="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                {badge && (
                    <span className="inline-flex items-center gap-2 bg-blue-100 text-blue-800 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                        {badge}
                    </span>
                )}
                <h1 className="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
                    {title}
                </h1>
                {subtitle && (
                    <p className="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                        {subtitle}
                    </p>
                )}
            </div>
        </section>
    );
}
