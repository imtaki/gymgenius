import { 
  Dumbbell, 
  TrendingUp, 
  Calendar, 
  Users, 
} from "lucide-react";
import { Button } from "@/components/ui/button";

export default function Footer() {
    const currentYear = new Date().getFullYear();

    const productLinks = [
        { name: "Features", href: "#" },
        { name: "Pricing", href: "#" },
        { name: "API", href: "#" },
    ];
    
    const supportLinks = [  
        { name: "Help Center", href: "#" },
        { name: "Contact", href: "#" },
        { name: "Status", href: "#" },
    ];

    const buttonBadges = [
        { icon: <Users className="h-4 w-4" />, href: "#" },
        { icon: <Calendar className="h-4 w-4" />, href: "#" },
        { icon: <TrendingUp className="h-4 w-4" />, href: "#" },
    ];

    return (
    <footer className="py-16 bg-muted/30">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-4 gap-8">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center space-x-2 mb-4">
                <Dumbbell className="h-8 w-8 text-primary" />
                <span className="text-2xl font-bold">GymGenius</span>
              </div>
              <p className="text-muted-foreground mb-4 max-w-md">
                The AI-powered fitness platform that helps you achieve your health and wellness goals with personalized training and nutrition guidance.
              </p>
              <div className="flex space-x-4">
                {buttonBadges.map((badge, index) => (
                  <Button key={index} variant="outline" size="icon" asChild>
                    <a href={badge.href} aria-label={`Link to ${badge.href}`}>
                      {badge.icon}
                    </a>
                  </Button>
                ))}
              </div>
            </div>
            <div>
              <h3 className="font-semibold mb-4">Product</h3>
              <ul className="space-y-2 text-muted-foreground">
              {productLinks.map((item)=> (
                  <li key={item.name}>
                  <a key={item.name} href={item.href} className="hover:text-foreground transition-colors">{item.name}</a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h3 className="font-semibold mb-4">Support</h3>
              <ul className="space-y-2 text-muted-foreground">
                {supportLinks.map((item)=> (
                  <li key={item.name}>
                  <a key={item.name} href={item.href} className="hover:text-foreground transition-colors">{item.name}</a>
                  </li>
                ))}
              </ul>
            </div>
          </div>
          <div className="border-t mt-12 pt-8 text-center text-muted-foreground">
            <p>&copy; {currentYear} GymGenius. All rights reserved.</p>
          </div>
        </div>
      </footer>
    );
}