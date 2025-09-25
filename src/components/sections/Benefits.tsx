import { Badge } from "@/components/ui/badge"
import { TrendingUp, CircleQuestionMark, Zap, Target, BarChart3, Calendar, Apple } from "lucide-react"

export default function Benefits() {
  const benefits = [
    {
      icon: Zap,
      text: "AI-powered workout recommendations",
      description: "Smart algorithms create personalized routines"
    },
    {
      icon: BarChart3,
      text: "Comprehensive progress tracking",
      description: "Monitor every aspect of your fitness journey"
    },
    {
      icon: Target,
      text: "Customizable training splits",
      description: "Flexible programs that adapt to your schedule"
    },
    {
      icon: Apple,
      text: "Nutrition and macro tracking",
      description: "Complete dietary monitoring and guidance"
    },
    {
      icon: TrendingUp,
      text: "Personal record monitoring",
      description: "Celebrate achievements and break barriers"
    },
    {
      icon: Calendar,
      text: "Calendar workout views",
      description: "Visual planning for consistent training"
    }
  ];

  return (
    <section id="benefits" className="py-20">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <Badge variant="outline" className="mb-4"><CircleQuestionMark />Why Choose GymGenius</Badge>
            <h2 className="text-3xl md:text-4xl font-bold mb-6">
              Achieve Your Goals <span className="text-primary">10x Faster</span>
            </h2>
            <p className="text-lg text-muted-foreground mb-8">
              Our AI-powered platform adapts to your unique fitness profile, providing personalized recommendations that evolve with your progress.
            </p>
            <div className="space-y-6">
              {benefits.map((benefit, index) => (
                <div key={index} className="flex items-start space-x-4 group">
                  <div className="shrink-0 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors duration-200">
                    <benefit.icon className="w-5 h-5 text-primary" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-foreground mb-1">{benefit.text}</h3>
                    <p className="text-sm text-muted-foreground">{benefit.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
          
          <div className="relative">
            <div className="aspect-square rounded-2xl border bg-linear-to-br from-primary/5 to-primary/10 p-8 flex items-center justify-center hover:shadow-lg transition-shadow duration-300">
              <div className="text-center">
                <div className="relative mb-6">
                  <div className="absolute inset-0 bg-primary/20 rounded-full blur-xl"></div>
                  <TrendingUp className="h-24 w-24 text-primary mx-auto relative z-10" />
                </div>
                <h3 className="text-2xl font-bold mb-3">Track Everything</h3>
                <p className="text-muted-foreground leading-relaxed">
                  Monitor your progress with detailed analytics and insights that help you stay motivated and on track
                </p>
              </div>
            </div>
            
            <div className="absolute -top-4 -right-4 w-8 h-8 bg-primary/20 rounded-full blur-sm"></div>
            <div className="absolute -bottom-6 -left-6 w-12 h-12 bg-primary/10 rounded-full blur-md"></div>
          </div>
        </div>
      </div>
    </section>
  )
}