const COLORS = {
    blue: "bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400",
    emerald: "bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400",
    red: "bg-red-50 dark:bg-red-900/40 text-red-600 dark:text-red-400",
    amber: "bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400",
    white: "bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10",
    solid: "bg-blue-600 text-white",
};

const SIZES = {
    sm: { box: "w-9 h-9 rounded-lg", icon: "w-4 h-4" },
    md: { box: "w-11 h-11 rounded-xl", icon: "w-5 h-5" },
    lg: { box: "w-14 h-14 rounded-2xl", icon: "w-7 h-7" },
};

export default function IconTile({ icon: Icon, color = "blue", size = "md", className = "" }) {
    const c = COLORS[color] ?? COLORS.blue;
    const s = SIZES[size] ?? SIZES.md;

    return (
        <div className={`${s.box} ${c} flex items-center justify-center flex-shrink-0 ${className}`}>
            <Icon className={s.icon} strokeWidth={2} />
        </div>
    );
}
