const ACCENT_COLORS = {
    blue: "text-blue-600",
    emerald: "text-emerald-600",
};

/**
 * A decorative, illustration-style panel used in place of a plain emoji.
 * Built from layered abstract shapes rather than photography, since we
 * don't have real product photography to place here.
 */
export default function SectionIllustration({ icon: Icon, accent = "blue" }) {
    return (
        <div className="relative bg-gradient-to-br from-blue-50 to-emerald-50 rounded-3xl p-10 sm:p-16 overflow-hidden min-h-[280px] flex items-center justify-center">
            {/* Ambient blobs */}
            <div className="absolute -top-10 -right-10 w-40 h-40 bg-blue-200/50 rounded-full blur-3xl" />
            <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-200/50 rounded-full blur-3xl" />

            {/* Dot grid texture */}
            <div
                className="absolute inset-0 opacity-[0.06]"
                style={{
                    backgroundImage: "radial-gradient(circle, #0f172a 1px, transparent 1px)",
                    backgroundSize: "18px 18px",
                }}
            />

            {/* Floating accent chips */}
            <div className="absolute top-8 left-8 w-11 h-11 bg-white rounded-xl shadow-lg flex items-center justify-center rotate-[-10deg]">
                <div className="w-2.5 h-2.5 bg-emerald-400 rounded-full" />
            </div>
            <div className="absolute bottom-10 right-10 w-9 h-9 bg-white rounded-lg shadow-lg flex items-center justify-center rotate-[8deg]">
                <div className="w-2 h-2 bg-blue-400 rounded-full" />
            </div>

            {/* Core icon card */}
            <div className="relative z-10 w-28 h-28 sm:w-32 sm:h-32 bg-white rounded-[1.75rem] shadow-xl flex items-center justify-center">
                <Icon
                    className={`w-14 h-14 sm:w-16 sm:h-16 ${ACCENT_COLORS[accent] ?? ACCENT_COLORS.blue}`}
                    strokeWidth={1.5}
                />
            </div>
        </div>
    );
}
