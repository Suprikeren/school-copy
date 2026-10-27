import { Link } from "@inertiajs/react";
import { cn } from "@/lib/utils";

export default function Pagination({ links }: { links: any[] }) {
    return (
        <div className="flex flex-wrap items-center gap-1 mt-4">
            {links.map((link, i) => (
                <Link
                    key={i}
                    href={link.url || "#"}
                    // Kita pakai dangerouslySetInnerHTML karena Laravel ngirim label "Next &raquo;"
                    dangerouslySetInnerHTML={{ __html: link.label }}
                    className={cn(
                        "px-3 py-1 text-sm border rounded-md transition-colors",
                        link.active
                            ? "bg-primary text-white border-primary" // Warna kalau aktif
                            : "bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-400 dark:border-gray-800",
                        !link.url && "opacity-50 cursor-not-allowed" // Kalau gak ada link (seperti di ujung)
                    )}
                />
            ))}
        </div>
    );
}
