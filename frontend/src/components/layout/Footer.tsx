import Link from "next/link";
import { Dumbbell, TrendingUp, Calendar, Users } from "lucide-react";

const PRODUCT_LINKS = [
  { name: "Features", href: "#features" },
  { name: "Pricing",  href: "#pricing"  },
  { name: "API",      href: "#"         },
];

const SUPPORT_LINKS = [
  { name: "Help Center", href: "#" },
  { name: "Contact",     href: "#" },
  { name: "Status",      href: "#" },
];

const SOCIAL_ICONS = [
  { icon: Users,     href: "#", label: "Community"  },
  { icon: Calendar,  href: "#", label: "Schedule"   },
  { icon: TrendingUp,href: "#", label: "Progress"   },
];

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer
      className="bg-zinc-950 border-t border-zinc-800 py-16"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid md:grid-cols-4 gap-10">

          
          <div className="md:col-span-2">
            <Link href="/" className="inline-flex items-center gap-2.5 mb-5 group">
              <div className="w-8 h-8 rounded-lg bg-lime-400 flex items-center justify-center group-hover:bg-lime-300 transition-colors">
                <Dumbbell className="h-4 w-4 text-zinc-900" />
              </div>
              <span className="text-sm font-bold text-zinc-100 uppercase tracking-widest">GymGenius</span>
            </Link>

            <p className="text-xs text-zinc-500 leading-relaxed max-w-sm mb-6">
              The AI-powered fitness platform that helps you achieve your health and
              wellness goals with personalized training and nutrition guidance.
            </p>

            <div className="flex items-center gap-2">
              {SOCIAL_ICONS.map(({ icon: Icon, href, label }) => (
                <a
                  key={label}
                  href={href}
                  aria-label={label}
                  className="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-lime-400 hover:border-lime-400/30 transition-all duration-200"
                >
                  <Icon className="h-3.5 w-3.5" />
                </a>
              ))}
            </div>
          </div>

          
          <div>
            <h3 className="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Product</h3>
            <ul className="space-y-3">
              {PRODUCT_LINKS.map((item) => (
                <li key={item.name}>
                  <a
                    href={item.href}
                    className="text-xs text-zinc-600 hover:text-zinc-200 transition-colors duration-200"
                  >
                    {item.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

         
          <div>
            <h3 className="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Support</h3>
            <ul className="space-y-3">
              {SUPPORT_LINKS.map((item) => (
                <li key={item.name}>
                  <a
                    href={item.href}
                    className="text-xs text-zinc-600 hover:text-zinc-200 transition-colors duration-200"
                  >
                    {item.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

        </div>

       
        <div className="mt-12 pt-6 border-t border-zinc-900 flex flex-col sm:flex-row items-center justify-between gap-2">
          <p className="text-xs text-zinc-700">
            © {currentYear} GymGenius. All rights reserved.
          </p>
          <div className="flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse" />
            <span className="text-xs text-zinc-700">All systems operational</span>
          </div>
        </div>

      </div>
    </footer>
  );
}