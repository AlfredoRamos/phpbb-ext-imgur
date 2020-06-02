# frozen_string_literal: true

require 'sassc'
require 'autoprefixer-rails'
require 'uglifier'
require 'oj'
require 'rubocop/rake_task'
require 'logger'

$stdout.sync = $stderr.sync = true

# Logger
logger = Logger.new($stdout)
logger.datetime_format = '%F %T %:z'
logger.formatter = proc do |severity, datetime, _progname, msg|
  "#{datetime} | #{severity} | #{msg}\n"
end

# Tests
RuboCop::RakeTask.new

namespace :build do
  # Input files
  files = {
    css: Dir.glob('styles/**/theme/css/*.css') + Dir.glob('adm/style/css/*.css'),
    js: Dir.glob('styles/**/theme/js/*.js') + Dir.glob('adm/style/js/*.js')
  }

  # Exclude minified files
  files[:css].delete_if { |file| file.end_with?('.min.css') }
  files[:js].delete_if { |file| file.end_with?('.min.js') }

  # Base build
  task :base, [:opts] do |_t, args|
    args[:opts][:output] = args[:opts][:input] unless args[:opts].key?(:output)
    args[:opts][:output] += '.tmp' if args[:opts][:output].eql?(args[:opts][:input])

    logger.info(format('Processing file: %<filename>s', filename: args[:opts][:input]))

    File.open(args[:opts][:output], 'w') do |f|
      css = File.read(args[:opts][:input])

      f.puts AutoprefixerRails.process(
        css,
        map: false,
        cascade: false,
        from: args[:opts][:input],
        to: args[:opts][:output],
        browsers: [
          '>= 1%',
          'last 1 major version',
          'not dead',
          'Chrome >= 45',
          'Firefox >= 38',
          'Edge >= 12',
          'Explorer >= 10',
          'iOS >= 9',
          'Safari >= 9',
          'Android >= 4.4',
          'Opera >= 30'
        ]
      ).css
    end

    if args[:opts][:output].index(/\.tmp$/).to_i.positive?
      logger.warn(format('Overwriting file: %<filename>s', filename: args[:opts][:input]))
      File.delete(args[:opts][:input])
      File.rename(args[:opts][:output], args[:opts][:input])
    end

    # Minify
    output = args[:opts][:input].gsub(/\.css$/, '.min.css')
    logger.info(format('Processing file: %<filename>s', filename: output))

    File.open(output, 'w') do |f|
      css = File.read(args[:opts][:input])

      f.puts SassC::Engine.new(
        css,
        style: :compressed,
        cache: false,
        syntax: :css,
        filename: output,
        sourcemap: :none
      ).render
    end
  end

  desc 'Build CSS files'
  task :css do
    logger.info('Building CSS files')

    files[:css].each do |file|
      Rake::Task['build:base'].reenable
      Rake::Task['build:base'].invoke(
        input: file,
        style: :expanded
      )
    end
  end

  desc 'Build JS files'
  task :js do
    logger.info('Building JS files')

    files[:js].each do |file|
      output = file.gsub(/\.js$/, '.min.js')

      logger.info(format('Processing file: %<filename>s', filename: output))

      File.open(output, 'w') do |f|
        js = File.read(file)

        f.puts Uglifier.compile(
          js,
          harmony: true
        )
      end
    end
  end

  desc 'Minify assets'
  task :minify do
    logger.info('Minifying assets')

    json = Oj.load_file('composer.json')
    ext = json['name'].split('/')
    base_dir = File.join('build', 'package', ext.first, ext.last)

    unless Dir.exist?(base_dir)
      logger.error(format('Directory not found: %<directory>s', directory: base_dir))
      abort
    end

    namespace_format = '@%<vendor>s_%<name>s/%<path>s/%<filename>s'

    Dir.chdir(base_dir) do
      template = Dir.glob('styles/**/template/**/*.html') + Dir.glob('adm/style/**/*.html')

      template.each do |file|
        html = old_html = File.read(file)

        # JS
        files[:js].each do |f|
          processed = []
          namespace = format(
            namespace_format,
            vendor: ext.first,
            name: ext.last,
            path: File.extname(f).sub('.', ''),
            filename: File.basename(f)
          )

          next unless html.include?(namespace)

          logger.info(format('Processing file: %<filename>s', filename: file)) unless processed.any?(file)
          processed.push(file)

          html = html.gsub(namespace, namespace.gsub(/\.js$/, '.min.js'))
        end

        # CSS
        files[:css].each do |f|
          processed = []
          namespace = format(
            namespace_format,
            vendor: ext.first,
            name: ext.last,
            path: File.extname(f).sub('.', ''),
            filename: File.basename(f)
          )

          next unless html.include?(namespace)

          logger.info(format('Processing file: %<filename>s', filename: file)) unless processed.any?(file)
          processed.push(file)

          html = html.gsub(namespace, namespace.gsub(/\.css$/, '.min.css'))
        end

        next if html === old_html

        logger.warn(format('Overwritting file: %<filename>s', filename: file))

        File.open(file, 'w') do |f|
          f.puts html
        end

        unless File.size(file).positive?
          logger.fatal(format('Generated empty file: %<filename>s', filename: file))
          abort
        end
      end
    end
  end

  desc 'Build all CSS files'
  task :all do
    Rake::Task['build:css'].invoke
    Rake::Task['build:js'].invoke
    Rake::Task['build:minify'].invoke
  end
end
